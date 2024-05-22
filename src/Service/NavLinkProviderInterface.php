<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BootstrapBundle\Component\Nav\NavLink;

interface NavLinkProviderInterface
{
    public function getNavLink(): NavLink;

    public function getVoterAttribute(): string;

    public function getVoterSubject(): mixed;
}
