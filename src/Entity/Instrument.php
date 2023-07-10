<?php

namespace App\Entity;

use App\Repository\InstrumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstrumentRepository::class)]
class Instrument
{
    #[ORM\Column(options: ["unsigned" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = 0;

    #[ORM\Column(length: 55)]
    private ?string $number = null;

    #[ORM\Column]
    private ?string $brand = null;

    #[ORM\Column]
    private bool $usable = true;

    #[ORM\Column(nullable: true)]
    private ?string $information = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "property", nullable: true)]
    private ?Property $property = null;

    #[ORM\ManyToOne(inversedBy: "instruments")]
    #[ORM\JoinColumn(name: "recipient", nullable: true, onDelete: "SET NULL")]
    private ?Member $recipient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "cat", nullable: false)]
    private ?Category $cat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "subcat")]
    private ?Subcategory $subcat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function isUsable(): bool
    {
        return $this->usable;
    }

    public function setUsable(bool $usable): static
    {
        $this->usable = $usable;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): static
    {
        $this->information = $information;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getRecipient(): ?Member
    {
        return $this->recipient;
    }

    public function setRecipient(?Member $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getCat(): ?Category
    {
        return $this->cat;
    }

    public function setCat(?Category $cat): static
    {
        $this->cat = $cat;

        return $this;
    }

    public function getSubcat(): ?Subcategory
    {
        return $this->subcat;
    }

    public function setSubcat(?Subcategory $subcat): static
    {
        $this->subcat = $subcat;

        return $this;
    }

    public function __toString()
    {
        return $this->cat . ' ' . $this->subcat;
    }
}