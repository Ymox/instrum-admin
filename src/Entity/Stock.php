<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = 0;

    #[ORM\Column]
    private ?string $what = null;

    #[ORM\Column(type: "smallint")]
    private ?int $amount = 0;

    #[ORM\OneToMany(targetEntity: Provision::class, mappedBy: "stock", cascade: ["persist"])]
    #[ORM\OrderBy(["member" => "ASC"])]
    private Collection $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWhat(): ?string
    {
        return $this->what;
    }

    public function setWhat(string $what): static
    {
        $this->what = $what;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection|Provision[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Provision $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setStock($this);
        }

        return $this;
    }

    public function removeMember(Provision $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getStock() === $this) {
                $member->setStock(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->what;
    }
}
