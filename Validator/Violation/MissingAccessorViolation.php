<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Violation;

use Visca\Bundle\DoctrineBundle\Validator\Violation\Abstracts\AbstractInvalidAccessorViolation;

/**
 * Class MissingAccessorViolation.
 */
class MissingAccessorViolation extends AbstractInvalidAccessorViolation
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return sprintf(
            'Missing method "%s" of class "%s"',
            $this->methodName,
            $this->className
        );
    }
}
