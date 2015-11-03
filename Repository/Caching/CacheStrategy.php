<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching;

use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;

/**
 * Class CacheStrategy.
 */
final class CacheStrategy implements ResultCachingStrategyInterface
{
    /**
     * @var int
     */
    private $lifetime;

    /**
     * CacheStrategy constructor.
     *
     * @param int $lifetime Time to live of the cache
     */
    public function __construct($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCache()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifetime()
    {
        return $this->lifetime;
    }
}
