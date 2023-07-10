<?php

namespace App\Entity;

use App\Repository\ProvisionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProvisionRepository::class)]
#[UniqueEntity(["member", "stock"])]
class Provision
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: "stocks")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: "members")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $stock = null;

    #[ORM\Column(nullable: true)]
    private ?string $details = null;

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }
}
