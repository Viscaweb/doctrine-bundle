<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Violation\Abstracts;

use Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces\ViolationInterface;

/**
 * Class AbstractInvalidAccessorViolation.
 */
abstract class AbstractInvalidAccessorViolation implements ViolationInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @param string $methodName
     */
    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
}
