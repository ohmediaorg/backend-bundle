<?php

namespace OHMedia\BackendBundle;

use OHMedia\BackendBundle\DependencyInjection\Compiler\NavPass;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaBackendBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new NavPass());
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
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
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import('../config/services.yaml');

        $containerConfigurator->parameters()
            ->set('oh_media_backend.tinymce.plugins', $config['tinymce']['plugins'])
            ->set('oh_media_backend.tinymce.toolbar', $config['tinymce']['toolbar'])
        ;

        $containerBuilder->registerForAutoconfiguration(AbstractNavItemProvider::class)
            ->addTag('oh_media_backend.nav_item_provider')
        ;
    }
}
