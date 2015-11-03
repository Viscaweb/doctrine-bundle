<?php

namespace Visca\Bundle\DoctrineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Visca\Bundle\DoctrineBundle\DependencyInjection\Compiler\AccessorAssertionsCompilerPass;

/**
 * Class ViscaDoctrineBundle.
 */
class ViscaDoctrineBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AccessorAssertionsCompilerPass());
    }
}
