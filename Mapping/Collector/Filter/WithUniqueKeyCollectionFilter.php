<?php

namespace Visca\Bundle\DoctrineBundle\Mapping\Collector\Filter;

use Doctrine\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use Visca\Bundle\DoctrineBundle\Generator\Interfaces\ClassGeneratorInterface;
use Visca\Bundle\DoctrineBundle\Mapping\Collector\ClassMetadataWithGenerator;

/**
 * Class WithUniqueKeyCollectionFilter.
 */
final class WithUniqueKeyCollectionFilter
{
    /**
     * @var ClassGeneratorInterface[] Generators
     */
    protected $classesGenerators;

    /**
     * WithUniqueKeyCollectionFilter constructor.
     *
     * @param ClassGeneratorInterface[] $classesGenerators Classes Generators
     */
    public function __construct(array $classesGenerators)
    {
        $this->classesGenerators = $classesGenerators;
    }

    /**
     * @param ClassMetadataCollection $metadataCollection
     *
     * @return ClassMetadataWithGenerator[]
     */
    public function filter(ClassMetadataCollection $metadataCollection)
    {
        $generators = $this->getClassesGenerators();
        /** @var ClassMetadataWithGenerator[] $metaDataArray */
        $metaDataArray = [];

        /*
         * Try to find a generator for each metadata
         */
        foreach ($metadataCollection->getMetadata() as $metaData) {
            $metaDataGenerator = null;
            foreach ($generators as $generator) {
                if ($generator->supports($metaData)) {
                    $metaDataGenerator = $generator;
                    break;
                }
            }
            if (!is_null($metaDataGenerator)) {
                $metaDataArray[] = new ClassMetadataWithGenerator(
                    $metaData,
                    $metaDataGenerator
                );
            }
        }

        return $metaDataArray;
    }

    /**
     * @return \Visca\Bundle\DoctrineBundle\Generator\Interfaces\ClassGeneratorInterface[]
     */
    public function getClassesGenerators()
    {
        return $this->classesGenerators;
    }
}
