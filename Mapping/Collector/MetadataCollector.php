<?php

namespace Visca\Bundle\DoctrineBundle\Mapping\Collector;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Visca\Bundle\DoctrineBundle\Mapping\Collector\Interfaces\MetadataCollectorInterface;

/**
 * Class MetadataCollector.
 */
final class MetadataCollector implements MetadataCollectorInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @param KernelInterface $kernel
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        KernelInterface $kernel,
        ManagerRegistry $managerRegistry
    ) {
        $this->kernel = $kernel;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function collect($name, $sourcePath)
    {
        $manager = new DisconnectedMetadataFactory(
            $this->managerRegistry
        );

        try {
            $bundle = $this->kernel->getBundle($name);

            if (!$bundle instanceof BundleInterface) {
                throw new Exception(
                    sprintf(
                        'Expecting an instance of "%s", "%s" given',
                        BundleInterface::class,
                        get_class($bundle)
                    )
                );
            }

            return $manager->getBundleMetadata($bundle);
        } catch (\InvalidArgumentException $e) {
            $name = strtr($name, '/', '\\');

            if (false !== $pos = strpos($name, ':')) {
                $aliasNameSpace = $this
                    ->managerRegistry
                    ->getAliasNamespace(substr($name, 0, $pos));
                $name = $aliasNameSpace.'\\'.substr($name, $pos + 1);
            }

            if (class_exists($name)) {
                return $manager->getClassMetadata(
                    $name,
                    $sourcePath
                );
            }

            return $manager->getNamespaceMetadata(
                $name,
                $sourcePath
            );
        }
    }
}
