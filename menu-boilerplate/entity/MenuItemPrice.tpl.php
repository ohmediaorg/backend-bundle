<?php echo "<?php\n"; ?>

namespace App\Entity;

use App\Repository\<?php echo $singular['pascal_case']; ?>ItemPriceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: <?php echo $singular['pascal_case']; ?>ItemPriceRepository::class)]
class <?php echo $singular['pascal_case']; ?>ItemPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    #[Assert\Length(max: 25)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\Positive]
    #[Assert\NotBlank]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?<?php echo $singular['pascal_case']; ?>Item $item = null;

    public function __toString(): string
    {
        return (string) $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getItem(): ?<?php echo $singular['pascal_case']; ?>Item
    {
        return $this->item;
    }

    public function setItem(?<?php echo $singular['pascal_case']; ?>Item $item): static
    {
        $this->item = $item;

        return $this;
    }
}
