<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BackendBundle\Security\Voter\SettingVoter;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\SettingsBundle\Entity\Setting;

class ScriptInjectionNavLinkProvider extends AbstractDeveloperOnlyNavLinkProvider
{
    public function getNavLink(): NavLink
    {
        return (new NavLink('Script Injection', 'settings_script_injection'))
            ->setIcon('code-slash');
    }

    public function getVoterAttribute(): string
    {
        return SettingVoter::SCRIPTS;
    }

    public function getVoterSubject(): mixed
    {
        return new Setting();
    }
}
