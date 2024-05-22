<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BackendBundle\Security\Voter\SettingVoter;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\SettingsBundle\Entity\Setting;

class SEONavLinkProvider extends AbstractSettingsNavLinkProvider
{
    public function getNavLink(): NavLink
    {
        return new NavLink('SEO', 'settings_seo');
    }

    public function getVoterAttribute(): string
    {
        return SettingVoter::SEO;
    }

    public function getVoterSubject(): mixed
    {
        return new Setting();
    }
}
