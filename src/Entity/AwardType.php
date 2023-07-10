<?php

namespace App\Entity;

use App\Repository\AwardTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AwardTypeRepository::class)]
class AwardType
{
    const COLUMN_SCMV = 'scmv';
    const COLUMN_BAND = 'band';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = 0;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(type: "smallint")]
    private ?int $years = 0;

    #[ORM\Column(length: 15)]
    private ?string $column = self::COLUMN_SCMV;

    #[ORM\ManyToOne(cascade: ["persist", "remove"])]
    private ?Status $statusToAward = null;

    #[ORM\OneToMany(targetEntity: Award::class, mappedBy: "awardType")]
    #[ORM\OrderBy(["year" => "DESC"])]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: Status::class, inversedBy: "awardTypes")]
    private Collection $eligibleStatuses;

    public static array $COLUMNS = [
        self::COLUMN_SCMV,
        self::COLUMN_BAND,
    ];

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->eligibleStatuses = new ArrayCollection();
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

    public function getYears(): ?int
    {
        return $this->years;
    }

    public function setYears(int $years): static
    {
        $this->years = $years;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function getStatusToAward(): ?Status
    {
        return $this->statusToAward;
    }

    public function setStatusToAward(?Status $statusToAward): static
    {
        $this->statusToAward = $statusToAward;

        return $this;
    }

    /**
     * @return Collection|Award[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Award $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setAwardType($this);
        }

        return $this;
    }

    public function removeMember(Award $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getAwardType() === $this) {
                $member->setAwardType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Status[]
     */
    public function getEligibleStatuses(): Collection
    {
        return $this->eligibleStatuses;
    }

    public function addEligibleStatus(Status $status): static
    {
        if (!$this->eligibleStatuses->contains($status)) {
            $this->eligibleStatuses[] = $status;
        }

        return $this;
    }

    public function removeEligibleStatus(Status $status): static
    {
        $this->eligibleStatuses->removeElement($status);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
