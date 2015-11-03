<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Exceptions;

use Exception;
use Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces\ViolationInterface;

/**
 * Class ViolationException.
 */
class ViolationException extends Exception
{
    /**
     * @var ViolationInterface[]
     */
    private $violations;

    /**
     * @param ViolationInterface[] $violations
     */
    public function __construct(array $violations)
    {
        $this->violations = $violations;
    }

    /**
     * @return ViolationInterface[]
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
