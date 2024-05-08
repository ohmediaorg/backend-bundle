<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TinymceExtension extends AbstractExtension
{
    private bool $rendered = false;
    private string $toolbar;

    public function __construct(private string $plugins, array $toolbar)
    {
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
        if ($this->rendered) {
            return;
        }

        $this->rendered = true;

        $shortcodes = $this->getShortcodes();

        return $env->render('@OHMediaBackend/tinymce_script.html.twig', [
            'plugins' => $this->plugins,
            'toolbar' => $this->toolbar,
            'shortcodes' => $shortcodes,
        ]);
    }

    private function getShortcodes(): array
    {
        $shortcodeProviders = [];

        $shortcodeProviders[] = [
            'title' => 'Testimonials',
            'shortcodes' => [
                [
                    'shortcode' => 'testimonials()',
                    'label' => 'All Testimonials',
                ],
                [
                    'shortcode' => 'testimonial()',
                    'label' => 'One Random Testimonial',
                ],
                [
                    'shortcode' => 'testimonial(1)',
                    'label' => 'Ryan Karikas (ID:1)',
                ],
            ],
        ];

        $shortcodeProviders[] = [
            'title' => 'Videos',
            'shortcodes' => [
                [
                    'shortcode' => 'video(2)',
                    'label' => '4K Long Relax Video with Music (ID:2)',
                ],
                [
                    'shortcode' => 'video(1)',
                    'label' => 'Meeting of the Black Republicans - Key & Peele (ID:1)',
                ],
                [
                    'shortcode' => 'video(3)',
                    'label' => 'What\'s My Name (ID:3)',
                ],
            ],
        ];

        // convert to TinyMCE Tab Panel syntax

        $tabs = [];

        foreach ($shortcodeProviders as $i => $shortcodeProvider) {
            $name = 'tab_'.$i;

            $selectbox = [
                'type' => 'selectbox',
                'name' => $name.'_shortcode',
                'label' => 'Shortcode',
                'items' => [],
            ];

            foreach ($shortcodeProvider['shortcodes'] as $shortcode) {
                $selectbox['items'][] = [
                    'value' => trim($shortcode['shortcode'], '{} '),
                    'text' => $shortcode['label'],
                ];
            }

            $tabs[] = [
                'name' => $name,
                'title' => $shortcodeProvider['title'],
                'items' => [$selectbox],
            ];
        }

        return $tabs;
    }
}
