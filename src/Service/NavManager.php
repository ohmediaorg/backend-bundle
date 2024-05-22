<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BootstrapBundle\Component\Nav\Nav;
use OHMedia\BootstrapBundle\Component\Nav\NavDropdown;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NavManager
{
    private array $navItemProviders = [];
    private array $developerOnlyNavLinkProviders = [];
    private NavDropdown $developerOnlyDropdown;
    private array $settingsNavLinkProviders = [];
    private NavDropdown $settingsDropdown;

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->developerOnlyDropdown = (new NavDropdown('Developer Only'))
            ->setIcon('shield-slash-fill');

        $this->settingsDropdown = (new NavDropdown('Settings'))
            ->setIcon('gear-fill');
    }

    public function addNavItemProvider(AbstractNavItemProvider $navItemProvider): self
    {
        $this->navItemProviders[] = $navItemProvider;

        return $this;
    }

    public function addDeveloperOnlyNavLinkProvider(
        AbstractDeveloperOnlyNavLinkProvider $developerOnlyNavLinkProvider
    ): self {
        $this->developerOnlyNavLinkProviders[] = $developerOnlyNavLinkProvider;

        return $this;
    }

    public function addSettingsNavLinkProvider(
        AbstractSettingsNavLinkProvider $settingsNavLinkProvider
    ): self {
        $this->settingsNavLinkProviders[] = $settingsNavLinkProvider;

        return $this;
    }

    public function getNav()
    {
        $navItems = [];

        foreach ($this->navItemProviders as $navItemProvider) {
            if ($navItem = $navItemProvider->getNavItem()) {
                $navItems[] = $navItem;
            }
        }

        $developerOnlyNavLinks = $this->getDeveloperOnlyNavLinks();

        foreach ($developerOnlyNavLinks as $navLink) {
            $this->developerOnlyDropdown->addLink($navLink);
        }

        $navItems[] = $this->developerOnlyDropdown;

        $settingsNavLinks = $this->getSettingsNavLinks();

        foreach ($settingsNavLinks as $navLink) {
            $this->settingsDropdown->addLink($navLink);
        }

        $navItems[] = $this->settingsDropdown;

        usort($navItems, function (NavItemInterface $a, NavItemInterface $b) {
            return $a->getText() <=> $b->getText();
        });

        $nav = new Nav();

        foreach ($navItems as $navItem) {
            $nav->addItem($navItem);
        }

        return $nav;
    }

    private function getDeveloperOnlyNavLinks(): array
    {
        return $this->getNavLinks(...$this->developerOnlyNavLinkProviders);
    }

    private function getSettingsNavLinks(): array
    {
        return $this->getNavLinks(...$this->settingsNavLinkProviders);
    }

    private function getNavLinks(NavLinkProviderInterface ...$navLinkProviders): array
    {
        $navLinks = [];

        foreach ($navLinkProviders as $navLinkProvider) {
            $attribute = $navLinkProvider->getVoterAttribute();
            $subject = $navLinkProvider->getVoterSubject();

            if ($this->authorizationChecker->isGranted($attribute, $subject)) {
                $navLink = $navLinkProvider->getNavLink();

                $navLinks[] = $navLink;
            }
        }

        usort($navLinks, function (NavLink $a, NavLink $b) {
            return $a->getText() <=> $b->getText();
        });

        return $navLinks;
    }
}
