<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Assertions\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata;
use Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces\ViolationInterface;

/**
 * Interface AssertionInterface.
 */
interface AssertionInterface
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return ViolationInterface[]
     */
    public function assert(ClassMetadata $metadata);
}
