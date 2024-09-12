<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BackendBundle\Security\Voter\EmailVoter;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\EmailBundle\Entity\Email;

class EmailsNavLinkProvider extends AbstractDeveloperOnlyNavLinkProvider
{
    public function getNavLink(): NavLink
    {
        return (new NavLink('Emails', 'email_index'))->setIcon('envelope-fill');
    }

    public function getVoterAttribute(): string
    {
        return EmailVoter::INDEX;
    }

    public function getVoterSubject(): mixed
    {
        return new Email();
    }
}
