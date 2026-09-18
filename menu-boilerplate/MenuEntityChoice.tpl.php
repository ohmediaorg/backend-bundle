<?php echo "<?php\n"; ?>

namespace App\Service\EntityChoice;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use App\Entity\<?php echo $singular['pascal_case']; ?>Item;
use App\Entity\<?php echo $singular['pascal_case']; ?>Section;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class <?php echo $singular['pascal_case']; ?>EntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return '<?php echo $singular['title']; ?>';
    }

    public function getEntities(): array
    {
        return [
            <?php echo $singular['pascal_case']; ?>::class,
            <?php echo $singular['pascal_case']; ?>Section::class,
            <?php echo $singular['pascal_case']; ?>Item::class,
        ];
    }
}
