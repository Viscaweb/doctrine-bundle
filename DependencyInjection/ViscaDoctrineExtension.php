<?php

namespace Visca\Bundle\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ViscaDoctrineExtension.
 */
class ViscaDoctrineExtension extends Extension
{
    const GENERATOR_UNIQUE_VALUES_CLASS_TEMPLATE = 'visca_doctrine.generator.unique_values_class.template';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $aliasArray = [
            'visca_doctrine.naming.classes' => $config['naming']['classes'],
            'visca_doctrine.naming.constant' => $config['naming']['constants'],
            'visca_doctrine.templating.engine' => $config['templating']['engine'],
            'visca_doctrine.manager_registry' => $config['manager_registry'],
            'visca_doctrine.kernel' => $config['kernel'],
        ];

        foreach ($aliasArray as $alias => $serviceId) {
            $container->setAlias($alias, $serviceId);
        }

        $parametersArray = [
            self::GENERATOR_UNIQUE_VALUES_CLASS_TEMPLATE => $config['generator']['unique_values_class']['template'],
        ];

        foreach ($parametersArray as $name => $value) {
            $container->setParameter($name, $value);
        }

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $this->loadCachingConfiguration($config['caching'], $container);
    }

    /**
     * @param array            $cachingConfig
     * @param ContainerBuilder $container
     */
    private function loadCachingConfiguration(
        array $cachingConfig,
        ContainerBuilder $container
    ) {
        $cachingChainDefinition = $container
            ->getDefinition('visca_doctrine.repository.caching.chain');

        foreach ($cachingConfig['entities'] as $entity) {
            $cachingChainDefinition->addMethodCall(
                'attach',
                [
                    $entity['class'],
                    new Reference($entity['strategy']),
                ]
            );
        }
    }
}
