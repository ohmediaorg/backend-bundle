<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ShortcodeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('shortcode', [$this, 'shortcode'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function shortcode(string $shortcode)
    {
        return '<code>{{'.trim($shortcode).'}}</code>';
    }
}
