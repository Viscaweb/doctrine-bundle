<?php

namespace Visca\Bundle\DoctrineBundle\Query\QueryBuilder;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Visca\Bundle\DoctrineBundle\Query\QueryBuilder\Interfaces\PredicateBuilderInterface;

/**
 * Class PredicateBuilder.
 */
final class PredicateBuilder implements PredicateBuilderInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     *
     * @return Query\Expr\Base
     */
    public function build(
        QueryBuilder $queryBuilder,
        array $criteria
    ) {
        $wherePredicates = $queryBuilder
            ->expr()
            ->andX();

        foreach ($criteria as $columnName => $value) {
            $wherePredicate = $this->createPredicate(
                $queryBuilder,
                $columnName,
                $value
            );

            $wherePredicates->add($wherePredicate);
        }

        return $wherePredicates;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return mixed
     */
    private function createPredicate(
        QueryBuilder $queryBuilder,
        $columnName,
        $value
    ) {
        if (is_array($value)) {
            return $queryBuilder
                ->expr()
                ->in("q.$columnName", ":$columnName");
        } elseif (is_scalar($value)) {
            return $queryBuilder
                ->expr()
                ->eq("q.$columnName", ":$columnName");
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Only scalar or array are valid, %s given',
                gettype($value)
            )
        );
    }
}
