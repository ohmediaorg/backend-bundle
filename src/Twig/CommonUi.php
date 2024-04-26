<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommonUi extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFunction('badge_success', [$this, 'badgeSuccess']),
            new TwigFunction('badge_info', [$this, 'badgeInfo']),
            new TwigFunction('badge_warning', [$this, 'badgeWarning']),
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
