<?php

namespace OHMedia\BackendBundle\Twig;

use OHMedia\FileBundle\Service\FileBrowser;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TinymceExtension extends AbstractExtension
{
    private bool $rendered = false;
    private string $toolbar;

    public function __construct(
        private FileBrowser $fileBrowser,
        private string $plugins,
        array $toolbar
    ) {
        $this->toolbar = implode(' | ', $toolbar);

        if (!$this->fileBrowser->isEnabled()) {
            $this->plugins = str_replace([' ohimagebrowser', 'ohimagebrowser '], '', $this->plugins);
            $this->toolbar = str_replace([' ohimagebrowser', 'ohimagebrowser '], '', $this->toolbar);
        }
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
        if ($this->rendered) {
            return;
        }

        $this->rendered = true;

        return $env->render('@OHMediaBackend/tinymce_script.html.twig', [
            'plugins' => $this->plugins,
            'toolbar' => $this->toolbar,
            'file_browser_enabled' => $this->fileBrowser->isEnabled(),
        ]);
    }
}
