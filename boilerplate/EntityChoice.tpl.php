<?php echo "<?php\n"; ?>

namespace App\Service\EntityChoice;

use App\Entity\<?php echo $singular['pascal_case']; ?>;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class <?php echo $singular['pascal_case']; ?>EntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return '<?php echo $plural['title']; ?>';
    }

    public function getEntities(): array
    {
        return [<?php echo $singular['pascal_case']; ?>::class];
    }
}
