<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $locality = null;

    #[ORM\Column(nullable: true)]
    private ?int $zip = null;

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): static
    {
        $this->locality = $locality;

        return $this;
    }

    public function getZip(): ?int
    {
        return $this->zip;
    }

    public function setZip(?int $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function __toString()
    {
        return $this->address . "\n" . $this->zip . ' ' . $this->locality;
    }
}
