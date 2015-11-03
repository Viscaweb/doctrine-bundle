<?php

namespace Visca\Bundle\DoctrineBundle\Mapping\Collector\Filter\Interfaces;

use Doctrine\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;

/**
 * Interface MetadataCollectionFilterInterface.
 */
interface MetadataCollectionFilterInterface
{
    /**
     * @param ClassMetadataCollection $metadataCollection
     *
     * @return ClassMetadataCollection
     */
    public function filter(ClassMetadataCollection $metadataCollection);
}
