<?php

namespace OHMedia\BackendBundle\Twig;

use OHMedia\BackendBundle\Service\NavManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavExtension extends AbstractExtension
{
    public function __construct(private NavManager $navManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('backend_nav', [$this, 'nav']),
        ];
    }

    public function nav()
    {
        return $this->navManager->getNav();
    }
}
