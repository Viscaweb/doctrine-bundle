<?php
namespace Visca\Bundle\DoctrineBundle\Generator\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Interface GeneratorInterface.
 */
interface ClassGeneratorInterface
{
    /**
     * @param ClassMetadataInfo $metaData Meta Data
     *
     * @return bool
     */
    public function supports(ClassMetadataInfo $metaData);

    /**
     * @param ClassMetadataInfo      $metadata        Meta Data
     * @param EntityManagerInterface $entityManager   Entity manager
     * @param string                 $destinationPath Destination Path
     *
     * @return bool
     */
    public function generate(
        ClassMetadataInfo $metadata,
        EntityManagerInterface $entityManager,
        $destinationPath
    );
}
