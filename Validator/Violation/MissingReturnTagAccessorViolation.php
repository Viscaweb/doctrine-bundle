<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Violation;

use Visca\Bundle\DoctrineBundle\Validator\Violation\Abstracts\AbstractInvalidAccessorViolation;

/**
 * Class MissingReturnTagAccessorViolation.
 */
class MissingReturnTagAccessorViolation extends AbstractInvalidAccessorViolation
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return sprintf(
            'Method "%s" of class "%s" does not have return tag',
            $this->methodName,
            $this->className
        );
    }
}
