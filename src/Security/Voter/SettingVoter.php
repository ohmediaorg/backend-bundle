<?php

namespace OHMedia\BackendBundle\Security\Voter;

use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\SettingsBundle\Entity\Setting;

class SettingVoter extends AbstractEntityVoter
{
    public const SEO = 'seo';
    public const SCRIPTS = 'scripts';

    protected function getAttributes(): array
    {
        return [
            self::SEO,
            self::SCRIPTS,
        ];
    }

    protected function getEntityClass(): string
    {
        return Setting::class;
    }

    protected function canSeo(Setting $setting, User $loggedIn): bool
    {
        return true;
    }

    protected function canScripts(Setting $setting, User $loggedIn): bool
    {
        return $loggedIn->isTypeDeveloper();
    }
}
