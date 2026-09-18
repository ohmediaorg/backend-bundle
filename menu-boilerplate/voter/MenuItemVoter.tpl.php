<?php

namespace App\Security\Voter;

use App\Entity\MenuItem;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class MenuItemVoter extends AbstractEntityVoter
{
    public const REORDER = 'reorder';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::REORDER,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return MenuItem::class;
    }

    protected function canReorder(MenuItem $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(MenuItem $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(MenuItem $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(MenuItem $menuItem, User $loggedIn): bool
    {
        return true;
    }
}
