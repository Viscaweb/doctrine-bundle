<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Abstracts;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Visca\Bundle\DoctrineBundle\Query\QueryBuilder\Interfaces\PredicateBuilderInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;

/**
 * Class AbstractEntityManager.
 */
abstract class AbstractEntityRepository implements ObjectRepository, Selectable
{
    /**
     * @var ResultCachingStrategyInterface
     */
    protected $resultCaching;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ClassMetadata
     */
    protected $class;

    /**
     * @var PredicateBuilderInterface
     */
    protected $predicateBuilder;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager                  $entityManager The EntityManager
     *                                                      to use.
     * @param ClassMetadata                  $class         The class
     *                                                      descriptor.
     * @param ResultCachingStrategyInterface $resultCaching
     * @param PredicateBuilderInterface      $predicateBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        ClassMetadata $class,
        ResultCachingStrategyInterface $resultCaching,
        PredicateBuilderInterface $predicateBuilder
    ) {
        $this->entityName = $class->name;
        $this->entityManager = $entityManager;
        $this->class = $class;
        $this->resultCaching = $resultCaching;
        $this->predicateBuilder = $predicateBuilder;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object|null The object if found.
     */
    public function find($id)
    {
        // Accept both composite key and single key
        if (!is_array($id)) {
            if ($this->class->isIdentifierComposite) {
                throw ORMInvalidArgumentException::invalidCompositeIdentifier();
            }

            $id = [$this->class->identifier[0] => $id];
        }

        // Do not support object, only scalar value
        foreach ($id as $value) {
            if (is_object($value)) {
                throw ORMInvalidArgumentException::invalidIdentifierBindingEntity(
                );
            }
        }

        $sortedIdentifiers = [];

        foreach ($this->class->identifier as $identifier) {
            if (!isset($id[$identifier])) {
                throw ORMException::missingIdentifierField(
                    $this->class->name,
                    $identifier
                );
            }

            $sortedIdentifiers[$identifier] = $id[$identifier];
            unset($id[$identifier]);
        }

        if ($id) {
            throw ORMException::unrecognizedIdentifierFields(
                $this->class->name,
                array_keys($id)
            );
        }

        try {
            $alias = 'q';

            $queryBuilder = $this
                ->createQueryBuilder($alias);

            $wherePredicates = [];

            foreach ($sortedIdentifiers as $identifier => $value) {
                $wherePredicates[] = $queryBuilder
                    ->expr()
                    ->eq(
                        $alias.'.'.$identifier,
                        ':'.$identifier
                    );
            }

            $andPredicates = $queryBuilder->expr();

            $queryBuilder
                ->add(
                    'where',
                    call_user_func_array([$andPredicates, 'andX'], $wherePredicates)
                );

            $queryBuilder->setParameters($sortedIdentifiers);

            $query = $queryBuilder->getQuery();

            $this->setCacheStrategy($query);

            return $query->getSingleResult();
        } catch (NoResultException $ex) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('q');

        $query = $queryBuilder->getQuery();
        $this->setCacheStrategy($query);

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $criteria,
        array $orderBy = [],
        $limit = null,
        $offset = null
    ) {
        $queryBuilder = $this
            ->createQueryBuilder('q');

        $query = $this->createQueryWith(
            $queryBuilder,
            $criteria,
            $orderBy,
            $limit,
            $offset
        );
        $this->setCacheStrategy($query);

        return $query->getResult();
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return object|null The object.
     */
    public function findOneBy(array $criteria)
    {
        $queryBuilder = $this
            ->createQueryBuilder('q');

        $result = $this
            ->createQueryWith($queryBuilder, $criteria)
            ->getResult();

        if (count($result) == 0) {
            return;
        }

        return $result[0];
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->getEntityName();
    }

    /**
     * Creates a new QueryBuilder instance that is pre populated for this
     * entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        $queryBuilder = $this
            ->entityManager
            ->createQueryBuilder()
            ->select($alias)
            ->from($this->entityName, $alias, $indexBy);

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Criteria $criteria)
    {
        $persister = $this
            ->entityManager
            ->getUnitOfWork()
            ->getEntityPersister($this->entityName);

        return new LazyCriteriaCollection($persister, $criteria);
    }

    /**
     * Returns the number of results matching the given criteria.
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria = [])
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('q');

        $queryBuilder
            ->select($queryBuilder->expr()->count('q'))
            ->setCacheable(false);

        $wherePredicates = $queryBuilder
            ->expr()
            ->andX();

        foreach (array_keys($criteria) as $columnName) {
            $wherePredicate = $queryBuilder
                ->expr()
                ->eq("q.$columnName", ":$columnName");
            $wherePredicates->add($wherePredicate);
        }

        $query = $queryBuilder
            ->where($wherePredicates)
            ->setParameters($criteria)
            ->getQuery();

        $this->setCacheStrategy($query);

        return intval($query->getSingleScalarResult());
    }

    /**
     * @deprecated Will be removed soon
     *
     * Adds support for magic finders.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return array|object The found entity/entities.
     *
     * @throws ORMException
     * @throws \BadMethodCallException If the method called is an invalid find*
     *                                 method or no find* method at all and
     *                                 therefore an invalid method call.
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $findBy = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $findBy = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with ".
                    'either findBy or findOneBy!'
                );
        }

        if (empty($arguments)) {
            throw ORMException::findByRequiresParameter($method.$findBy);
        }

        $fieldName = lcfirst(
            \Doctrine\Common\Util\Inflector::classify($findBy)
        );

        if ($this->class->hasField($fieldName)
            || $this->class->hasAssociation(
                $fieldName
            )
        ) {
            switch (count($arguments)) {
                case 1:
                    return $this->$method([$fieldName => $arguments[0]]);

                case 2:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1]
                    );

                case 3:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1],
                        $arguments[2]
                    );

                case 4:
                    return $this->$method(
                        [$fieldName => $arguments[0]],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3]
                    );

                default:
                    // Do nothing
            }
        }

        throw ORMException::invalidFindByCall(
            $this->entityName,
            $fieldName,
            $method.$findBy
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     * @param array        $orderBy
     * @param int|null     $limit
     * @param int|null     $offset
     *
     * @return Query
     */
    protected function createQueryWith(
        QueryBuilder $queryBuilder,
        array $criteria,
        array $orderBy = [],
        $limit = null,
        $offset = null
    ) {
        if (count($criteria) > 0) {
            $wherePredicates = $this
                ->predicateBuilder
                ->build(
                    $queryBuilder,
                    $criteria
                );

            $queryBuilder = $this
                ->createQueryBuilder('q')
                ->where($wherePredicates);

            foreach ($criteria as $columnName => $value) {
                if (is_array($value)) {
                    $queryBuilder->setParameter(
                        $columnName,
                        $value,
                        \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
                    );
                } else {
                    $queryBuilder->setParameter($columnName, $value);
                }
            }
        }

        if (count($orderBy) > 0) {
            foreach ($orderBy as $fieldName => $orientation) {
                $queryBuilder->orderBy("q.$fieldName", $orientation);
            }
        }

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }

        $query = $queryBuilder->getQuery();
        $this->setCacheStrategy($query);

        return $query;
    }

    /**
     * @return string
     */
    protected function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param Query $query The Query object
     */
    protected function setCacheStrategy($query)
    {
        if ($this->resultCaching->hasCache()) {
            $query->useResultCache(
                true,
                $this->resultCaching->getCacheLifetime()
            );
        }
    }
}
