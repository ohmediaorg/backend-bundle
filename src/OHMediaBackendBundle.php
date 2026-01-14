<?php

namespace OHMedia\BackendBundle;

use OHMedia\BackendBundle\DependencyInjection\Compiler\NavPass;
use OHMedia\BackendBundle\Service\AbstractDeveloperOnlyNavLinkProvider;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BackendBundle\Service\AbstractSettingsNavLinkProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaBackendBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new NavPass());
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import('../config/services.yaml');

        $containerBuilder->registerForAutoconfiguration(AbstractNavItemProvider::class)
            ->addTag('oh_media_backend.nav_item_provider')
        ;

        $containerBuilder->registerForAutoconfiguration(AbstractDeveloperOnlyNavLinkProvider::class)
            ->addTag('oh_media_backend.developer_only_nav_link_provider')
        ;

        $containerBuilder->registerForAutoconfiguration(AbstractSettingsNavLinkProvider::class)
            ->addTag('oh_media_backend.settings_nav_link_provider')
        ;

        $this->registerWidget($containerBuilder);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $containerBuilder)
    {
        $resource = '@OHMediaBackend/form/multi_save_widget.html.twig';

        $containerBuilder->setParameter('twig.form.resources', array_merge(
            $containerBuilder->getParameter('twig.form.resources'),
            [$resource]
        ));
    }
}
