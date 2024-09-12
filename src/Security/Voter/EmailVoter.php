<?php

namespace OHMedia\BackendBundle\Security\Voter;

use OHMedia\EmailBundle\Entity\Email;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class EmailVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const VIEW = 'view';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::VIEW,
        ];
    }

    protected function getEntityClass(): string
    {
        return Email::class;
    }

    protected function canIndex(Email $email, User $loggedIn): bool
    {
        return $loggedIn->isTypeDeveloper();
    }

    protected function canView(Email $email, User $loggedIn): bool
    {
        return $loggedIn->isTypeDeveloper();
    }
}
