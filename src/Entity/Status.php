<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[Gedmo\Tree(type: "nested")]
#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = 0;

    #[ORM\Column]
    private ?string $name = null;

    #[Gedmo\TreeLeft]
    #[ORM\Column]
    private ?int $lft = 0;

    #[Gedmo\TreeRight]
    #[ORM\Column]
    private ?int $rgt = 0;
    

    #[Gedmo\TreeLevel]
    #[ORM\Column]
    private ?int $lvl = 0;
    
    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne]
    private ?Status $root = null;
    
    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(inversedBy: "children")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Status $parent = null;

    #[ORM\OneToMany(targetEntity: Status::class, mappedBy: "parent")]
    #[ORM\OrderBy(["lft" => "ASC"])]
    private Collection $children;

    #[ORM\ManyToMany(targetEntity: AwardType::class, mappedBy: "eligibleStatuses")]
    private Collection $awardTypes;

    #[ORM\ManyToMany(targetEntity: Member::class, mappedBy: "statuses")]
    private Collection $members;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->awardTypes = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

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

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): static
    {
        $this->lft = $lft;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): static
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): static
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getRoot(): ?self
    {
        return $this->root;
    }

    public function setRoot(?self $root): static
    {
        $this->root = $root;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Status[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Status $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Status $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->addStatus($this);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        if ($this->members->removeElement($member)) {
            $member->removeStatus($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * return Collection|AwardType[]
     */
    public function getAwardTypes(): Collection
    {
        return $this->awardTypes;
    }

    public function addAwardType(AwardType $awardType): static
    {
        if (!$this->awardTypes->contains($awardType)) {
            $this->awardTypes[] = $awardType;
            $awardType->addStatus($this);
        }

        return $this;
    }

    public function removeAwardType(AwardType $awardType): static
    {
        if ($this->awardTypes->removeElement($awardType)) {
            $awardType->removeStatus($this);
        }

        return $this;
    }
}
