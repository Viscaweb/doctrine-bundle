<?php

namespace Visca\Bundle\DoctrineBundle\Validator\Assertions;

use Doctrine\ORM\Mapping\ClassMetadata;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use ReflectionClass;
use Visca\Bundle\DoctrineBundle\Validator\Assertions\Interfaces\AssertionInterface;
use Visca\Bundle\DoctrineBundle\Validator\Collector\Mapping\NullableAssociationCollector;
use Visca\Bundle\DoctrineBundle\Validator\Collector\Mapping\NullableFieldCollector;
use Visca\Bundle\DoctrineBundle\Validator\Extractor\Entity\EntityAccessorReflector;
use Visca\Bundle\DoctrineBundle\Validator\Violation\Interfaces\ViolationInterface;
use Visca\Bundle\DoctrineBundle\Validator\Violation\MissingAccessorViolation;
use Visca\Bundle\DoctrineBundle\Validator\Violation\MissingReturnTagAccessorViolation;
use Visca\Bundle\DoctrineBundle\Validator\Violation\NotAllowNullTypeAccessorViolation;

/**
 * Class AssertionNullablePropertyHasNullInReturnType.
 */
final class NullablePropertyHasNullInReturnTypeAssertion implements AssertionInterface
{
    /**
     * @var NullableFieldCollector
     */
    private $nullableFieldCollector;

    /**
     * @var NullableAssociationCollector
     */
    private $nullableAssociationCollector;

    /**
     * @var EntityAccessorReflector
     */
    private $entityAccessorReflector;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->nullableFieldCollector = new NullableFieldCollector();
        $this->nullableAssociationCollector =
            new NullableAssociationCollector();
        $this->entityAccessorReflector = new EntityAccessorReflector();
    }

    /**
     * {@inheritdoc}
     */
    public function assert(ClassMetadata $metadata)
    {
        $reflection = new ReflectionClass($metadata->getName());

        $fields = array_merge(
            $this->nullableFieldCollector->collect($metadata),
            $this->nullableAssociationCollector->collect($metadata)
        );

        /** @var ViolationInterface[] $violations */
        $violations = [];

        foreach ($fields as $fieldName) {
            $this->assertGetterSpecifyNullReturnType(
                $reflection,
                $fieldName,
                $violations
            );
        }

        return $violations;
    }

    /**
     * @param ReflectionClass $reflection
     * @param string          $fieldName
     * @param array           $violations
     */
    private function assertGetterSpecifyNullReturnType(
        ReflectionClass $reflection,
        $fieldName,
        array &$violations
    ) {
        $className = $reflection->name;

        $getterMethod = $this->entityAccessorReflector
            ->getGetter(
                $className,
                $fieldName
            );

        if (null === $getterMethod) {
            $violations[] = new MissingAccessorViolation(
                $className,
                $fieldName
            );

            return;
        }

        $reflectionMethod = $reflection->getMethod($getterMethod);
        $doc = $reflectionMethod->getDocComment();

        $phpdoc = new DocBlock($doc);
        if (!$phpdoc->hasTag('return')) {
            $violations[] = new MissingReturnTagAccessorViolation(
                $reflection->name,
                $getterMethod
            );
        }

        $returnTags = $phpdoc->getTagsByName('return');
        /** @var ReturnTag[] $returnTags */
        if (!$this->areReturnTagsContainingNull($returnTags)) {
            $violations[] = new NotAllowNullTypeAccessorViolation(
                $reflection->name,
                $getterMethod
            );
        }
    }

    /**
     * @param ReturnTag[] $returnTags
     *
     * @return bool
     */
    private function areReturnTagsContainingNull(array $returnTags)
    {
        foreach ($returnTags as $returnTag) {
            $types = array_map(
                'strtolower',
                explode('|', $returnTag->getType())
            );
            if (in_array('null', $types)) {
                return true;
            }
        }

        return false;
    }
}
