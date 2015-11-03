<?php

namespace Visca\Bundle\DoctrineBundle\Validator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Visca\Bundle\DoctrineBundle\Validator\Assertions\Interfaces\AssertionInterface;
use Visca\Bundle\DoctrineBundle\Validator\Exceptions\ViolationException;
use Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces\ViolationInterface;

/**
 * Class AccessorValidator.
 */
final class AccessorValidator
{
    /**
     * @var AssertionInterface[]
     */
    private $assertions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->assertions = [];
    }

    /**
     * @param AssertionInterface $assertion
     */
    public function addAssertion(AssertionInterface $assertion)
    {
        $this->assertions[get_class($assertion)] = $assertion;
    }

    /**
     * @param ClassMetadata $metadata The metadata for the entity the validate the accessors
     *
     * @throws ViolationException
     */
    public function validate(ClassMetadata $metadata)
    {
        /** @var ViolationInterface[] $violations */
        $violations = [];
        foreach ($this->assertions as $assertion) {
            $violations = array_merge(
                $violations,
                $assertion->assert($metadata)
            );
        }

        if (count($violations) > 0) {
            throw new ViolationException($violations);
        }
    }
}
