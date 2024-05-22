<?php

namespace OHMedia\BackendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NavPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has('oh_media_backend.nav_manager')) {
            return;
        }

        $definition = $container->findDefinition('oh_media_backend.nav_manager');

        $tagged = $container->findTaggedServiceIds('oh_media_backend.nav_item_provider');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addNavItemProvider', [new Reference($id)]);
        }

        $tagged = $container->findTaggedServiceIds('oh_media_backend.developer_only_nav_link_provider');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addDeveloperOnlyNavLinkProvider', [new Reference($id)]);
        }

        $tagged = $container->findTaggedServiceIds('oh_media_backend.settings_nav_link_provider');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addSettingsNavLinkProvider', [new Reference($id)]);
        }
    }
}
