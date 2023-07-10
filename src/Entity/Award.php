<?php

namespace App\Entity;

use App\Repository\AwardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[UniqueEntity(["member", "awardType"])]
class Award
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: "awards")]
    private ?Member $member = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: AwardType::class, inversedBy: "members")]
    private ?AwardType $awardType = null;

    #[ORM\Column(columnDefinition: "YEAR(4)")]
    private ?int $year;

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getAwardType(): ?AwardType
    {
        return $this->awardType;
    }

    public function setAwardType(?AwardType $awardType): static
    {
        $this->awardType = $awardType;

        return $this;
    }
}
