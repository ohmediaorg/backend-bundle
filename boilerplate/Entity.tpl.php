<?php echo "<?php\n"; ?>

namespace App\Entity;

use App\Repository\<?php echo $singular['pascal_case']; ?>Repository;
<?php if ($has_reorder) { ?>
use Doctrine\DBAL\Types\Types;
<?php } ?>
use Doctrine\ORM\Mapping as ORM;
<?php if ($is_publishable) { ?>
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
<?php } ?>
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
// use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: <?php echo $singular['pascal_case']; ?>Repository::class)]
class <?php echo $singular['pascal_case']."\n"; ?>
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
<?php if ($has_reorder) { ?>

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ordinal = 9999;
<?php } ?>
<?php if ($is_publishable) { ?>

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $published_at = null;
<?php } ?>

    public function __toString(): string
    {
        return '<?php echo $singular['title']; ?> #'.$this->id;
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
<?php if ($is_publishable) { ?>

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeImmutable $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published_at && DateTimeUtil::isPast($this->published_at);
    }

    public function isScheduled(): bool
    {
        return $this->published_at && DateTimeUtil::isFuture($this->published_at);
    }
<?php } ?>
}
