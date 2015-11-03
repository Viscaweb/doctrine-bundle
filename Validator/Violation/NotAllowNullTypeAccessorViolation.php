<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Violation;

use Visca\Bundle\DoctrineBundle\Validator\Violation\Abstracts\AbstractInvalidAccessorViolation;

/**
 * Class NotAllowNullTypeAccessorViolation.
 */
class NotAllowNullTypeAccessorViolation extends AbstractInvalidAccessorViolation
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return sprintf(
            'Method "%s" of class "%s" does not specify null as return type',
            $this->methodName,
            $this->className
        );
    }
}
