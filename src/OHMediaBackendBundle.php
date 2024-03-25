<?php

namespace OHMedia\BackendBundle;

use OHMedia\BackendBundle\DependencyInjection\Compiler\NavPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OHMediaBackendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new NavPass());
    }
}
