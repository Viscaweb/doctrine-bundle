<?php

namespace Visca\Bundle\DoctrineBundle\Query\QueryBuilder\Interfaces;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PredicateBuilder.
 */
interface PredicateBuilderInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     *
     * @return Query\Expr\Base
     */
    public function build(QueryBuilder $queryBuilder, array $criteria);
}
