<?php

namespace Visca\Bundle\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('visca_doctrine');

        $rootNode
            ->children()
                ->scalarNode('manager_registry')->defaultValue('doctrine')->end()
                ->scalarNode('kernel')->defaultValue('kernel')->end()
                ->arrayNode('naming')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('classes')->defaultValue('visca_doctrine.naming.classes.default')->end()
                        ->scalarNode('constants')->defaultValue('visca_doctrine.naming.constants.default')->end()
                    ->end()
                ->end()
                ->arrayNode('templating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('engine')->defaultValue('templating.engine.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('unique_values_class')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('ViscaDoctrineBundle::class.php.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('caching')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('entities')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('class')->end()
                                    ->scalarNode('strategy')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
