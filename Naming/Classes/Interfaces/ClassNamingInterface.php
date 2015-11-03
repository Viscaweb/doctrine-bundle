<?php

namespace Visca\Bundle\DoctrineBundle\Naming\Classes\Interfaces;

/**
 * Class DefaultClassNaming.
 */
interface ClassNamingInterface
{
    /**
     * @param $path
     *
     * @return string
     */
    public function getNamespace($path);

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getClassname($path);
}
