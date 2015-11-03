<?php

namespace Visca\Bundle\DoctrineBundle\Naming\Constant\Interfaces;

/**
 * Interface ConstantNamingInterface.
 */
interface ConstantNamingInterface
{
    /**
     * @param string $className     The name of the main entity that contains the unique key
     * @param string $propertyName  The name of the field that is unique
     * @param string $propertyValue The value, most likely the id
     *
     * @return string The name of the constant
     */
    public function getName($className, $propertyName, $propertyValue);
}
