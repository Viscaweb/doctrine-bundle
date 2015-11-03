<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces;

/**
 * Interface ResultCachingStrategyInterface.
 */
interface ResultCachingStrategyInterface
{
    /**
     * @return bool
     */
    public function hasCache();

    /**
     * @return int Cache lifetime in seconds
     */
    public function getCacheLifetime();
}
