<?php

namespace App\Entity;

use App\Repository\MenuItemPriceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuItemPriceRepository::class)]
class MenuItemPrice
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
    private ?MenuItem $item = null;

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

    public function getItem(): ?MenuItem
    {
        return $this->item;
    }

    public function setItem(?MenuItem $item): static
    {
        $this->item = $item;

        return $this;
    }
}
