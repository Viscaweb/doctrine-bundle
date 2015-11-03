<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Collector\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class NullableFieldCollector.
 */
final class NullableFieldCollector
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    public function collect(ClassMetadata $metadata)
    {
        $fields = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $field = $metadata->getFieldMapping($fieldName);
            $nullable = isset($field['nullable']) ? $field['nullable'] : false;

            if ($nullable) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }
}
