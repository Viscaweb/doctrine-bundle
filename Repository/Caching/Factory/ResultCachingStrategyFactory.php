<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching\Factory;

use Visca\Bundle\DoctrineBundle\Repository\Caching\CacheStrategy;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\NoCacheStrategy;

/**
 * Class ResultCachingStrategyFactory.
 */
final class ResultCachingStrategyFactory
{
    /**
     * @param bool $hasCache Explicitly tell whether the strategy has or not caching
     * @param int  $lifetime TTL of cache in seconds
     *
     * @return ResultCachingStrategyInterface
     */
    public function create($hasCache, $lifetime = 0)
    {
        if (!$hasCache) {
            return new NoCacheStrategy();
        }

        return new CacheStrategy($lifetime);
    }
}
