<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Extractor\Entity;

use ReflectionClass;

/**
 * Class EntityAccessorReflector.
 */
final class EntityAccessorReflector
{
    /**
     * @param string $className
     * @param string $propertyName
     *
     * @return null|string
     */
    public function getGetter($className, $propertyName)
    {
        $reflection = new ReflectionClass($className);

        $possibleGetters = [
            'get'.$propertyName,
            'is'.$propertyName,
            'has'.$propertyName,
        ];

        foreach ($possibleGetters as $possibleGetter) {
            if ($reflection->hasMethod($possibleGetter)) {
                return $possibleGetter;
            }
        }

        return;
    }
}
