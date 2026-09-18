<?php echo "<?php\n"; ?>

namespace App\Security\Voter;

use App\Entity\Menu;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class MenuVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Menu::class;
    }

    protected function canIndex(Menu $menu, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Menu $menu, User $loggedIn): bool
    {
        return true;
    }

    protected function canView(Menu $menu, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Menu $menu, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Menu $menu, User $loggedIn): bool
    {
        return true;
    }
}
