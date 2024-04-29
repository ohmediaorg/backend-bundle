<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BadgeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('badge_success', [$this, 'badgeSuccess'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('badge_info', [$this, 'badgeInfo'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('badge_warning', [$this, 'badgeWarning'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function badgeSuccess(string $message): string
    {
        return '<span class="badge text-bg-success">' . $message . '</span>';
    }

    public function badgeInfo(string $message): string
    {
        return '<span class="badge text-bg-info">' . $message . '</span>';
    }

    public function badgeWarning(string $message): string
    {
        return '<span class="badge text-bg-warning">' . $message . '</span>';
    }
}
