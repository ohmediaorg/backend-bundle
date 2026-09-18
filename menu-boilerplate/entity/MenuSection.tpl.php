<?php

namespace App\Entity;

use App\Repository\MenuSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuSectionRepository::class)]
class MenuSection
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

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[Assert\NotBlank]
    private ?Menu $menu = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, MenuItem>
     */
    #[ORM\OneToMany(targetEntity: MenuItem::class, mappedBy: 'section', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['ordinal' => \SortDirection::Ascending])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

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

    public function getSlug(): string
    {
        $slugger = new AsciiSlugger();

        return $slugger->slug(strtolower($this->name));
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

    /**
     * @return Collection<int, MenuItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(MenuItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setSection($this);
        }

        return $this;
    }

    public function removeItem(MenuItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getSection() === $this) {
                $item->setSection(null);
            }
        }

        return $this;
    }
}
