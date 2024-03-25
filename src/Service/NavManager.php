<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BootstrapBundle\Component\Nav\Nav;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;

class NavManager
{
    private array $navItemProviders = [];

    public function addNavItemProvider(AbstractNavItemProvider $navItemProvider): self
    {
        $this->navItemProviders[] = $navItemProvider;

        return $this;
    }

    public function getNavItemProviders(): array
    {
        return $this->navItemProvider;
    }

    public function getNav()
    {
        $navItems = [];

        foreach ($this->navItemProviders as $navItemProvider) {
            if ($navItem = $navItemProvider->getNavItem()) {
                $navItems[] = $navItem;
            }
        }

        usort($navItems, function (NavItemInterface $a, NavItemInterface $b) {
            return $a->getText() <=> $b->getText();
        });

        $nav = new Nav();

        foreach ($navItems as $navItem) {
            $nav->addItem($navItem);
        }

        return $nav;
    }
}
