<?php echo "<?php\n"; ?>

namespace App\Service\Backend\Nav;

use App\Entity\Menu;
use App\Security\Voter\MenuVoter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class MenuNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(MenuVoter::INDEX, new Menu())) {
            return (new NavLink('Menus', 'menu_index'))
                ->setIcon('fork-knife');
        }

        return null;
    }
}
