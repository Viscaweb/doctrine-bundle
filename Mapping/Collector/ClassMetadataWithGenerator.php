<?php
namespace Visca\Bundle\DoctrineBundle\Mapping\Collector;

use Doctrine\ORM\Mapping\ClassMetadata;
use Visca\Bundle\DoctrineBundle\Generator\Interfaces\ClassGeneratorInterface;

/**
 * Class ClassMetadataWithGenerator
 */
class ClassMetadataWithGenerator
{
    /**
     * @var ClassMetadata MetaData
     */
    protected $metaData;

    /**
     * @var ClassGeneratorInterface Generator
     */
    protected $classGenerator;

    /**
     * ClassMetadataWithGeneratorsCollection constructor.
     *
     * @param ClassMetadata           $metaData
     * @param ClassGeneratorInterface $classGenerator
     */
    public function __construct(
        ClassMetadata $metaData,
        ClassGeneratorInterface $classGenerator
    ) {
        $this->metaData = $metaData;
        $this->classGenerator = $classGenerator;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * @return ClassGeneratorInterface
     */
    public function getClassGenerator()
    {
        return $this->classGenerator;
    }
}
