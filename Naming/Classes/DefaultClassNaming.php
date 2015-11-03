<?php

namespace Visca\Bundle\DoctrineBundle\Naming\Classes;

use Visca\Bundle\DoctrineBundle\Naming\Classes\Interfaces\ClassNamingInterface;

/**
 * Class DefaultClassNaming.
 */
final class DefaultClassNaming implements ClassNamingInterface
{
    const SEPARATOR = '\\';

    /**
     * @param $path
     *
     * @return string
     */
    public function getNamespace($path)
    {
        $parts = explode(self::SEPARATOR, $path);
        array_pop($parts);
        $namespace = implode(self::SEPARATOR, $parts);

        return $namespace;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getClassname($path)
    {
        $parts = explode(self::SEPARATOR, $path);
        $className = array_pop($parts);

        return $className;
    }
}
