<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces;

/**
 * Interface ViolationInterface.
 */
interface ViolationInterface
{
    /**
     * @return string
     */
    public function getMessage();
}
