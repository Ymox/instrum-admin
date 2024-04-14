<?php

namespace App\Entity;

use App\Entity\Embeddable\Address;
use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[UniqueEntity("mobile", groups: ["Default"], message: "member.mobile.not_unique")]
#[UniqueEntity(["firstname", "lastname"], groups: ["Default"], message: "member.duplicate")]
class Member
{
    const TITLE_MX = 'Monsiame';
    const TITLE_MR = 'Monsieur';
    const TITLE_MS = 'Madame';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = 0;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $title = null;

    #[ORM\Column]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?string $firstname = null;

    #[ORM\Embedded(columnPrefix: false)]
    private ?Address $address = null;

    #[ORM\Column(length: 20, nullable: true, unique: true)]
    private ?string $mobile = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $birthday = null;

    #[ORM\Column(columnDefinition: "YEAR(4)", nullable: true)]
    private ?int $scmv = null;

    #[ORM\Column(columnDefinition: Types::SMALLINT)]
    private int $scmvCorrection = 0;

    #[ORM\Column(columnDefinition: "YEAR(4)", nullable: true)]
    private ?int $band = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "cat_id")]
    private ?Category $cat = null;

    #[ORM\OneToMany(targetEntity: Instrument::class, mappedBy: "recipient", cascade: ["persist"])]
    private Collection $instruments;

    #[ORM\OneToMany(targetEntity: Provision::class, mappedBy: "member", cascade: ["persist", "remove"])]
    private Collection $stocks;

    #[ORM\OneToMany(targetEntity: Award::class, mappedBy: "member", cascade: ["persist", "remove"])]
    private Collection $awards;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Note::class, orphanRemoval: true)]
    #[ORM\OrderBy(['updatedAt' => 'DESC'])]
    private Collection $informations;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: "relations")]
    #[ORM\JoinTable("member_relation")]
    #[ORM\JoinColumn(name: "relation_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "related_id", referencedColumnName: "id")]
    private Collection $relateds;

    #[ORM\ManyToMany(targetEntity: Member::class, mappedBy: "relateds")]
    private Collection $relations;

    #[ORM\ManyToMany(targetEntity: Status::class, inversedBy: "members")]
    #[ORM\OrderBy(["lft" => "ASC"])]
    private Collection $statuses;

    #[ORM\ManyToMany(targetEntity: Level::class, inversedBy: "members")]
    #[ORM\JoinTable(name: "student_level")]
    private Collection $levels;

    public static array $TITLES = [
        self::TITLE_MS,
        self::TITLE_MR,
        self::TITLE_MX,
    ];

    public function __construct()
    {
        $this->address = new Address();
        $this->instruments = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->awards = new ArrayCollection();
        $this->relateds = new ArrayCollection();
        $this->relations = new ArrayCollection();
        $this->statuses = new ArrayCollection();
        $this->levels = new ArrayCollection();
        $this->informations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getScmv(): ?int
    {
        return $this->scmv;
    }

    public function setScmv(?int $scmv): static
    {
        $this->scmv = $scmv;

        return $this;
    }

    public function getScmvCorrection(): ?int
    {
        return $this->scmvCorrection;
    }

    public function setScmvCorrection(?int $scmvCorrection): static
    {
        $this->scmvCorrection = $scmvCorrection;

        return $this;
    }

    public function getBand(): ?int
    {
        return $this->band;
    }

    public function setBand(?int $band): static
    {
        $this->band = $band;

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

    public function getRelateds(): ?Collection
    {
        return $this->relateds;
    }

    public function addRelated(self $related): static
    {
        if (!$this->relateds->contains($related)) {
            $this->relateds[] = $related;
            $related->addRelation($this);
        }

        return $this;
    }

    public function removeRelated(self $related): static
    {
        if ($this->relateds->removeElement($related)) {
            $related->removeRelation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Instrument[]
     */
    public function getInstruments(): Collection
    {
        return $this->instruments;
    }

    public function addInstrument(Instrument $instrument): static
    {
        if (!$this->instruments->contains($instrument)) {
            $this->instruments[] = $instrument;
            $instrument->setRecipient($this);
        }

        return $this;
    }

    public function removeInstrument(Instrument $instrument): static
    {
        if ($this->instruments->removeElement($instrument)) {
            // set the owning side to null (unless already changed)
            if ($instrument->getRecipient() === $this) {
                $instrument->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Provision[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Provision $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setMember($this);
        }

        return $this;
    }

    public function removeStock(Provision $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getMember() === $this) {
                $stock->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Award[]
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $award): static
    {
        if (!$this->awards->contains($award)) {
            $this->awards[] = $award;
            $award->setMember($this);
        }

        return $this;
    }

    public function removeAward(Award $award): static
    {
        if ($this->awards->removeElement($award)) {
            $award->setMember(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getInformations(): Collection
    {
        return $this->informations;
    }

    public function addInformation(Note $information): static
    {
        if (!$this->informations->contains($information)) {
            $this->informations->add($information);
            $information->setMember($this);
        }

        return $this;
    }

    public function removeInformation(Note $information): static
    {
        if ($this->informations->removeElement($information)) {
            // set the owning side to null (unless already changed)
            if ($information->getMember() === $this) {
                $information->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(Member $relation): static
    {
        if (!$this->relations->contains($relation)) {
            $this->relations[] = $relation;
            $relation->addRelated($this);
        }

        return $this;
    }

    public function removeRelation(Member $relation): static
    {
        if ($this->relations->removeElement($relation)) {
            $relation->removeRelated($this);
        }

        return $this;
    }

    /**
     * @return Collection|Level[]
     */
    public function getLevels(): Collection
    {
        return $this->levels;
    }

    public function addLevel(Level $level): static
    {
        if (!$this->levels->contains($level)) {
            $this->levels[] = $level;
        }

        return $this;
    }

    public function removeLevel(Level $level): static
    {
        $this->levels->removeElement($level);

        return $this;
    }

    /**
     * @return Collection|Status[]
     */
    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    public function addStatus(Status $status): static
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses[] = $status;
        }

        return $this;
    }

    public function removeStatus(Status $status): static
    {
        $this->statuses->removeElement($status);

        return $this;
    }

    public function __toString()
    {
        if ($this->title) {
            $display = $this->firstname . ' ' . $this->lastname;
        } else {
            $display = $this->lastname . ' ' . $this->firstname;
        }

        return $display;
    }

    public function getAge(): int
    {
        if ($this->birthday) {
            return (new \DateTimeImmutable())->diff($this->birthday)->y;
        }

        return 0;
    }
}