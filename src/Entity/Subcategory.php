<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Subcategory
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = 0;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: "subcategories")]
    #[ORM\JoinColumn(name: "cat_id", nullable: false)]
    private ?Category $cat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function __toString()
    {
        return $this->name;
    }
}
