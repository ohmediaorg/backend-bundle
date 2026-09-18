<?php echo "<?php\n"; ?>

namespace App\Security\Voter;

use App\Entity\MenuSection;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class MenuSectionVoter extends AbstractEntityVoter
{
    public const REORDER = 'reorder';
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::REORDER,
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return MenuSection::class;
    }

    protected function canReorder(MenuSection $menuSection, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(MenuSection $menuSection, User $loggedIn): bool
    {
        return true;
    }

    protected function canView(MenuSection $menuSection, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(MenuSection $menuSection, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(MenuSection $menuSection, User $loggedIn): bool
    {
        return true;
    }
}
