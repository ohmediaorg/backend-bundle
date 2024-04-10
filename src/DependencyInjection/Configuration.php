<?php

namespace OHMedia\BackendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('oh_media_backend');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('tinymce')
                  ->children()
                    ->scalarNode('plugins')
                        ->defaultValue('code link lists')
                    ->end()
                    ->arrayNode('toolbar')
                        ->scalarPrototype()->end()
                        ->defaultValue([
                            'undo redo',
                            'blocks',
                            'bold italic numlist bullist',
                            'alignleft aligncenter alignright alignjustify',
                            'outdent indent',
                        ])
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
