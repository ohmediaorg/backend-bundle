<?php echo "<?php\n"; ?>

namespace App\Security\Voter;

use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class <?php echo $singular['pascal_case']; ?>ItemVoter extends AbstractEntityVoter
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
        return <?php echo $singular['pascal_case']; ?>Item::class;
    }

    protected function canReorder(<?php echo $singular['pascal_case']; ?>Item $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(<?php echo $singular['pascal_case']; ?>Item $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(<?php echo $singular['pascal_case']; ?>Item $menuItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(<?php echo $singular['pascal_case']; ?>Item $menuItem, User $loggedIn): bool
    {
        return true;
    }
}
