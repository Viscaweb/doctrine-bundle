<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Collector\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class NullableAssociationCollector.
 */
final class NullableAssociationCollector
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    public function collect(ClassMetadata $metadata)
    {
        $fields = [];

        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if (!isset($associationMapping['joinColumns'])) {
                continue;
            }

            if (!isset($associationMapping['joinColumns'][0])) {
                continue;
            }

            $fieldName = $associationMapping['fieldName'];

            $nullable = isset($associationMapping['joinColumns'][0]['nullable']) ? $associationMapping['joinColumns'][0]['nullable'] : true;

            if ($nullable) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }
}
