<?php echo "<?php\n"; ?>

namespace App\Service\Backend\Nav;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use App\Security\Voter\<?php echo $singular['pascal_case']; ?>Voter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class <?php echo $singular['pascal_case']; ?>NavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(<?php echo $singular['pascal_case']; ?>Voter::INDEX, new <?php echo $singular['pascal_case']; ?>())) {
            return (new NavLink('<?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'))
                ->setIcon('<?php echo $icon; ?>');
        }

        return null;
    }
}
