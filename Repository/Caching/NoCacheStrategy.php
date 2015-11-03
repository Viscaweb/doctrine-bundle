<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching;

use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;

/**
 * Class NoCacheStrategy.
 */
final class NoCacheStrategy implements ResultCachingStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasCache()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifetime()
    {
        return 0;
    }
}
