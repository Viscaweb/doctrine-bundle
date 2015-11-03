<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching\Chain\Interfaces;

use InvalidArgumentException;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;

/**
 * Interface RepositoryCachingStrategyChainInterface.
 */
interface RepositoryCachingStrategyChainInterface
{
    /**
     * @param string                         $entityName
     * @param ResultCachingStrategyInterface $resultCachingStrategy
     */
    public function attach(
        $entityName,
        ResultCachingStrategyInterface $resultCachingStrategy
    );

    /**
     * @param string $entityName
     *
     * @return ResultCachingStrategyInterface
     *
     * @throws InvalidArgumentException If no strategy exists
     */
    public function get($entityName);

    /**
     * @param string $entityName
     *
     * @return bool
     */
    public function has($entityName);
}
