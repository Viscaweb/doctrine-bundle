<?php

namespace Visca\Bundle\DoctrineBundle\Repository\Caching\Chain;

use InvalidArgumentException;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Chain\Interfaces\RepositoryCachingStrategyChainInterface;
use Visca\Bundle\DoctrineBundle\Repository\Caching\Interfaces\ResultCachingStrategyInterface;

/**
 * Class RepositoryChain.
 */
final class RepositoryCachingStrategyChain implements RepositoryCachingStrategyChainInterface
{
    /**
     * @var ResultCachingStrategyInterface[]
     */
    private $strategies;

    /**
     * RepositoryChain constructor.
     */
    public function __construct()
    {
        $this->strategies = [];
    }

    /**
     * {@inheritdoc}
     */
    public function attach(
        $entityName,
        ResultCachingStrategyInterface $resultCachingStrategy
    ) {
        $hash = $this->getKey($entityName);
        $this->strategies[$hash] = $resultCachingStrategy;
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    private function getKey(
        $entityName
    ) {
        return $entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function get(
        $entityName
    ) {
        $hash = $this->getKey($entityName);

        if (!isset($this->strategies[$hash])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No result caching strategy found for entity "%s"',
                    $entityName
                )
            );
        }

        return $this->strategies[$hash];
    }

    /**
     * {@inheritdoc}
     */
    public function has($entityName)
    {
        $hash = $this->getKey($entityName);

        return isset($this->strategies[$hash]);
    }
}
