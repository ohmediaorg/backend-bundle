<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TinymceExtension extends AbstractExtension
{
    private $plugins;
    private $toolbar;

    public function __construct(string $plugins, array $toolbar)
    {
        $this->plugins = $plugins;
        $this->toolbar = implode(' | ', $toolbar);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tinymce_script', [$this, 'tinymceScript'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function tinymceScript(Environment $env)
    {
        return $env->render('@OHMediaBackend/tinymce_script.html.twig', [
            'plugins' => $this->plugins,
            'toolbar' => $this->toolbar,
        ]);
    }
}
