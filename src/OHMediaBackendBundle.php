<?php

namespace OHMedia\BackendBundle;

use OHMedia\BackendBundle\ContentLinks\AbstractContentLinkProvider;
use OHMedia\BackendBundle\DependencyInjection\Compiler\ContentLinkPass;
use OHMedia\BackendBundle\DependencyInjection\Compiler\NavPass;
use OHMedia\BackendBundle\DependencyInjection\Compiler\ShortcodePass;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BackendBundle\Shortcodes\AbstractShortcodeProvider;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaBackendBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ContentLinkPass());
        $container->addCompilerPass(new NavPass());
        $container->addCompilerPass(new ShortcodePass());
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('tinymce')
                  ->children()
                    ->scalarNode('plugins')
                        ->defaultValue('code link lists ohshortcodes ohfilebrowser ohcontentlink')
                    ->end()
                    ->arrayNode('toolbar')
                        ->scalarPrototype()->end()
                        ->defaultValue([
                            'undo redo',
                            'blocks ohshortcodes ohfilebrowser ohcontentlink',
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

        $containerBuilder->registerForAutoconfiguration(AbstractContentLinkProvider::class)
            ->addTag('oh_media_backend.content_link_provider')
        ;

        $containerBuilder->registerForAutoconfiguration(AbstractNavItemProvider::class)
            ->addTag('oh_media_backend.nav_item_provider')
        ;

        $containerBuilder->registerForAutoconfiguration(AbstractShortcodeProvider::class)
            ->addTag('oh_media_backend.shortcode_provider')
        ;
    }
}
