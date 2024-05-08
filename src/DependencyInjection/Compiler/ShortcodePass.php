<?php

namespace OHMedia\BackendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has('oh_media_backend.shortcode_manager')) {
            return;
        }

        $definition = $container->findDefinition('oh_media_backend.shortcode_manager');

        $tagged = $container->findTaggedServiceIds('oh_media_backend.shortcode_provider');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addShortcodeProvider', [new Reference($id)]);
        }
    }
}
