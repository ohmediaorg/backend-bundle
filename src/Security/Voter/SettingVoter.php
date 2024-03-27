<?php

namespace OHMedia\BackendBundle\Security\Voter;

use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\SettingsBundle\Entity\Setting;

class SettingVoter extends AbstractEntityVoter
{
    public const META = 'meta';
    public const SCRIPTS = 'scripts';

    protected function getAttributes(): array
    {
        return [
            self::META,
            self::SCRIPTS,
        ];
    }

    protected function getEntityClass(): string
    {
        return Setting::class;
    }

    protected function canMeta(Setting $setting, User $loggedIn): bool
    {
        return true;
    }

    protected function canScripts(Setting $setting, User $loggedIn): bool
    {
        return $loggedIn->isDeveloper();
    }
}
