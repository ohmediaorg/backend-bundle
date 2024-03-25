<?php

namespace OHMedia\BackendBundle\Service;

use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractNavItemProvider
{
    abstract public function getNavItem(): ?NavItemInterface;

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    final protected function isGranted(mixed $attribute, mixed $subject = null): bool
    {
        return $this->authorizationChecker->isGranted($attribute, $subject);
    }
}
