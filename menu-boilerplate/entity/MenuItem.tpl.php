<?php echo "<?php\n"; ?>

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\FileBundle\Entity\File;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ordinal = 9999;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $published_at = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[Assert\NotBlank]
    private ?MenuSection $section = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?File $image = null;

    #[ORM\Column(nullable: true)]
    private ?bool $favourite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $organic = null;

    #[ORM\Column(nullable: true)]
    private ?bool $spicy = null;

    #[ORM\Column(nullable: true)]
    private ?bool $dairy_free = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eggs = null;

    #[ORM\Column(nullable: true)]
    private ?bool $vegan = null;

    #[ORM\Column(nullable: true)]
    private ?bool $vegetarian = null;

    #[ORM\Column(nullable: true)]
    private ?bool $gluten_free = null;

    /**
     * @var Collection<int, MenuItemPrice>
     */
    #[ORM\OneToMany(targetEntity: MenuItemPrice::class, mappedBy: 'item', orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    #[Assert\Count(min: 1, minMessage: 'You must have at least one price.')]
    private Collection $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(int $ordinal): self
    {
        $this->ordinal = $ordinal;

        return $this;
    }

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

    public function getSection(): ?MenuSection
    {
        return $this->section;
    }

    public function setSection(?MenuSection $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isFavourite(): ?bool
    {
        return $this->favourite;
    }

    public function setFavourite(?bool $favourite): static
    {
        $this->favourite = $favourite;

        return $this;
    }

    public function isOrganic(): ?bool
    {
        return $this->organic;
    }

    public function setOrganic(?bool $organic): static
    {
        $this->organic = $organic;

        return $this;
    }

    public function isSpicy(): ?bool
    {
        return $this->spicy;
    }

    public function setSpicy(?bool $spicy): static
    {
        $this->spicy = $spicy;

        return $this;
    }

    public function isDairyFree(): ?bool
    {
        return $this->dairy_free;
    }

    public function setDairyFree(?bool $dairy_free): static
    {
        $this->dairy_free = $dairy_free;

        return $this;
    }

    public function isEggs(): ?bool
    {
        return $this->eggs;
    }

    public function setEggs(?bool $eggs): static
    {
        $this->eggs = $eggs;

        return $this;
    }

    public function isVegan(): ?bool
    {
        return $this->vegan;
    }

    public function setVegan(?bool $vegan): static
    {
        $this->vegan = $vegan;

        return $this;
    }

    public function isVegetarian(): ?bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(?bool $vegetarian): static
    {
        $this->vegetarian = $vegetarian;

        return $this;
    }

    public function isGlutenFree(): ?bool
    {
        return $this->gluten_free;
    }

    public function setGlutenFree(?bool $gluten_free): static
    {
        $this->gluten_free = $gluten_free;

        return $this;
    }

    public function hasTags(): bool
    {
        return $this->favourite || $this->dairy_free || $this->eggs || $this->gluten_free || $this->organic || $this->spicy || $this->vegan || $this->vegetarian;
    }

    /**
     * @return Collection<int, MenuItemPrice>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(MenuItemPrice $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setItem($this);
        }

        return $this;
    }

    public function removePrice(MenuItemPrice $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getItem() === $this) {
                $price->setItem(null);
            }
        }

        return $this;
    }
}
