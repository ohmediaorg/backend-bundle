<?php

namespace OHMedia\BackendBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'datetime',
                [$this, 'formatDateTime'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    public function formatDateTime(
        Environment $env,
        \DateTimeInterface $dateTime,
        $dateFormat = 'M j, Y',
        $timeFormat = 'g:ia',
        $timezone = null
    ) {
        return $env->render('@OHMediaBackend/widget/datetime.html.twig', [
            'date_time' => $dateTime,
            'date_format' => $dateFormat,
            'time_format' => $timeFormat,
            'timezone' => $timezone,
        ]);
    }
}
