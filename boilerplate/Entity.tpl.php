<?php echo "<?php\n"; ?>

namespace App\Entity;

use App\Repository\<?php echo $singular['pascal_case']; ?>Repository;
<?php if ($has_reorder) { ?>
use Doctrine\DBAL\Types\Types;
<?php } ?>
use Doctrine\ORM\Mapping as ORM;
use OHMedia\SecurityBundle\Entity\Traits\BlameableTrait;

#[ORM\Entity(repositoryClass: <?php echo $singular['pascal_case']; ?>Repository::class)]
class <?php echo $singular['pascal_case']."\n"; ?>
{
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
<?php if ($has_reorder) { ?>

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ordinal = 9999;
<?php } ?>

    public function __toString(): string
    {
        return '<?php echo $singular['title']; ?> #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
<?php if ($has_reorder) { ?>

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(int $ordinal): self
    {
        $this->ordinal = $ordinal;

        return $this;
    }
<?php } ?>
}
