<?php

namespace Visca\Bundle\DoctrineBundle\Mapping\Collector\Interfaces;

use Doctrine\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;

/**
 * Interface MetadataCollectorInterface.
 */
interface MetadataCollectorInterface
{
    /**
     * @param string $name       Generates entity classes and method stubs from your mapping information
     * @param string $sourcePath The path where to generate entities when it cannot be guessed
     *
     * @return ClassMetadataCollection
     */
    public function collect($name, $sourcePath);
}
