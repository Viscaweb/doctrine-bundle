<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Factory;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
use Visca\Bundle\DoctrineBundle\Query\QueryBuilder\Interfaces\PredicateBuilderInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Chain\Interfaces\RepositoryCachingStrategyChainInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\NoCacheStrategy;

/**
 * Class RepositoryFactory.
 */
final class RepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var PredicateBuilderInterface
     */
    protected $predicateBuilder;
    /**
     * The list of EntityRepository instances.
     *
     * @var ObjectRepository[]
     */
    private $repositoryList = [];

    /**
     * @var RepositoryCachingStrategyChainInterface
     */
    private $cachingStrategyChain;

    /**
     * RepositoryFactory constructor.
     *
     * @param RepositoryCachingStrategyChainInterface $cachingStrategyChain
     * @param PredicateBuilderInterface               $predicateBuilder
     */
    public function __construct(
        RepositoryCachingStrategyChainInterface $cachingStrategyChain,
        PredicateBuilderInterface $predicateBuilder
    ) {
        $this->cachingStrategyChain = $cachingStrategyChain;
        $this->predicateBuilder = $predicateBuilder;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository(
        EntityManagerInterface $entityManager,
        $entityName
    ) {
        $cachingStrategy = $this
            ->getCachingStrategy($entityName);

        $repositoryHash = $entityManager
                ->getClassMetadata($entityName)
                ->getName().spl_object_hash($entityManager);

        if (isset($this->repositoryList[$repositoryHash])) {
            return $this->repositoryList[$repositoryHash];
        }

        $this->repositoryList[$repositoryHash] = $this
            ->createRepository(
                $entityManager,
                $entityName,
                $cachingStrategy
            );

        return $this->repositoryList[$repositoryHash];
    }

    /**
     * @param $entityName
     *
     * @return ResultCachingStrategyInterface
     */
    private function getCachingStrategy($entityName)
    {
        if ($this->cachingStrategyChain->has($entityName)) {
            return $this
                ->cachingStrategyChain
                ->get($entityName);
        }

        return new NoCacheStrategy();
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param EntityManagerInterface         $entityManager The EntityManager instance.
     * @param string                         $entityName    The name of the entity.
     * @param ResultCachingStrategyInterface $resultCaching
     *
     * @return ObjectRepository
     */
    private function createRepository(
        EntityManagerInterface $entityManager,
        $entityName,
        ResultCachingStrategyInterface $resultCaching
    ) {
        /* @var $metadata ClassMetadata */
        $metadata = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata
            ->customRepositoryClassName ?: $entityManager
            ->getConfiguration()
            ->getDefaultRepositoryClassName();

        return new $repositoryClassName(
            $entityManager,
            $metadata,
            $resultCaching,
            $this->predicateBuilder
        );
    }
}
