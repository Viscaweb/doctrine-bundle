<?php

namespace Visca\Bundle\DoctrineBundle\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use InvalidArgumentException;
use Symfony\Component\Templating\EngineInterface;
use Visca\Bundle\DoctrineBundle\Generator\Interfaces\ClassGeneratorInterface;
use Visca\Bundle\DoctrineBundle\Naming\Classes\Interfaces\ClassNamingInterface;
use Visca\Bundle\DoctrineBundle\Naming\Constant\Interfaces\ConstantNamingInterface;

/**
 * Class UniqueFieldsGenerator.
 */
final class UniqueFieldsGenerator implements ClassGeneratorInterface
{
    /**
     * @var ClassNamingInterface
     */
    private $classNaming;

    /**
     * @var ConstantNamingInterface
     */
    private $constantNaming;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * @param EngineInterface         $templateEngine
     * @param ConstantNamingInterface $constantNaming
     * @param ClassNamingInterface    $classNaming
     * @param string                  $templateFile
     */
    public function __construct(
        ClassNamingInterface $classNaming,
        ConstantNamingInterface $constantNaming,
        EngineInterface $templateEngine,
        $templateFile
    ) {
        $this->classNaming = $classNaming;
        $this->constantNaming = $constantNaming;
        $this->templateEngine = $templateEngine;
        $this->templateFile = $templateFile;
    }

    /**
     * @param ClassMetadataInfo $metaData Meta Data
     *
     * @return bool
     */
    public function supports(ClassMetadataInfo $metaData)
    {
        return $this->hasUniqueKey($metaData);
    }

    /**
     * @param ClassMetadataInfo $metaData
     *
     * @return bool
     */
    private function hasUniqueKey(ClassMetadataInfo $metaData)
    {
        $fieldNames = array_merge(
            $metaData->getFieldNames()
        );

        foreach ($fieldNames as $fieldName) {
            if ($metaData->isUniqueField($fieldName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ClassMetadataInfo      $metadata
     * @param EntityManagerInterface $entityManager
     * @param string                 $destinationPath
     */
    public function generate(
        ClassMetadataInfo $metadata,
        EntityManagerInterface $entityManager,
        $destinationPath
    ) {
        $fieldNames = array_merge(
            $metadata->getFieldNames()
        );

        $repository = $entityManager->getRepository($metadata->getName());
        $entities = $repository->findAll();

        foreach ($fieldNames as $fieldName) {
            // Process only the entity having a single identifier (Ex: id)
            if ($metadata->isUniqueField($fieldName)
                && count($metadata->getIdentifier()) == 1
            ) {
                $this->generateClassContent(
                    $metadata,
                    $fieldName,
                    $entities,
                    $destinationPath
                );
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param string            $fieldName
     * @param array             $entities
     * @param string            $destinationPath
     *
     * @return string
     */
    private function generateClassContent(
        ClassMetadataInfo $metadata,
        $fieldName,
        array $entities,
        $destinationPath
    ) {
        $className = $metadata->getName();
        $baseClassName = $this->classNaming->getClassname($className);
        $namespace = $this->classNaming->getNamespace($className);

        $constantNamespace = $namespace.'\\'.ucfirst($fieldName);
        $constantBaseClassName = $baseClassName.ucfirst($fieldName);
        $constantClassName = $constantNamespace.'\\'.$constantBaseClassName;

        $identifierName = $metadata->getIdentifier()[0];

        $constantList = $this->buildConstantsList(
            $identifierName,
            $fieldName,
            $className,
            $entities
        );

        if (count($entities) == 0) {
            return;
        }

        $classContent = $this
            ->templateEngine
            ->render(
                $this->templateFile,
                [
                    'className' => $constantBaseClassName,
                    'namespace' => $constantNamespace,
                    'constantsList' => $constantList,
                ]
            );

        $classPath =
            $destinationPath.str_replace('\\', '/', $constantClassName).'.php';

        $classDir = dirname($classPath);

        if (!file_exists($classDir)) {
            mkdir($classDir, 0777, true);
        }

        file_put_contents($classPath, $classContent);
    }

    /**
     * @param string $identifierName
     * @param string $fieldName
     * @param string $className      Of the main entity
     * @param array  $entities
     *
     * @return array
     */
    private function buildConstantsList(
        $identifierName,
        $fieldName,
        $className,
        array $entities
    ) {
        $constantsList = [];

        foreach ($entities as $entity) {
            // Check the presence of the value getter
            $valueGetter = 'get'.$fieldName;
            if (!method_exists($entity, $valueGetter)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Missing getter "%s" in class "%s"',
                        $valueGetter,
                        get_class($entity)
                    )
                );
            }
            $value = $entity->$valueGetter();

            // Check the presence of the identifier getter
            $idGetter = 'get'.$identifierName;
            if (!method_exists($entity, $idGetter)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Missing getter "%s" in class "%s"',
                        $idGetter,
                        get_class($entity)
                    )
                );
            }
            $constantValue = $entity->$idGetter();

            $constantName = $this->constantNaming->getName(
                $className,
                $fieldName,
                $value
            );

            $constantsList[$constantName] = $constantValue;
        }

        return $constantsList;
    }
}
