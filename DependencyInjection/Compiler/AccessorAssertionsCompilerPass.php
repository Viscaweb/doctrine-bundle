<?php

namespace Visca\Bundle\DoctrineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AccessorAssertionsCompilerPass.
 */
final class AccessorAssertionsCompilerPass implements CompilerPassInterface
{
    const VALIDATOR_SERVICE_ID = 'visca_doctrine.validator.accessor';
    const TAG = 'visca_doctrine.validator.accessor.assertion';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::VALIDATOR_SERVICE_ID)) {
            return;
        }

        $definition = $container->getDefinition(self::VALIDATOR_SERVICE_ID);

        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach (array_keys($taggedServices) as $serviceId) {
            $definition->addMethodCall(
                'addAssertion',
                [new Reference($serviceId)]
            );
        }
    }
}
