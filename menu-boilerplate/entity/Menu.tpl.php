<?php echo "<?php\n"; ?>

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
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

    #[ORM\Column(length: 50)]
    #[Assert\Length(max: 50)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<int, MenuSection>
     */
    #[ORM\OneToMany(targetEntity: MenuSection::class, mappedBy: 'menu', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['ordinal' => \SortDirection::Ascending])]
    private Collection $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
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

    /**
     * @return Collection<int, MenuSection>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(MenuSection $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setMenu($this);
        }

        return $this;
    }

    public function removeSection(MenuSection $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getMenu() === $this) {
                $section->setMenu(null);
            }
        }

        return $this;
    }
}
