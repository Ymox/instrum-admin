<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ["username"], message: "app.user.username.not_unique")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = 0;

    #[ORM\Column(length: 25)]
    private ?string $username = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column]
    private int $nbConnect = 0;

    #[ORM\Column]
    private ?\DateTime $lastConnection = null;

    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getNbConnect(): ?int
    {
        return $this->nbConnect;
    }

    public function setNbConnect(int $nbConnect): static
    {
        $this->nbConnect = $nbConnect;

        return $this;
    }
    public function getLastConnection(): ?\DateTime
    {
        return $this->lastConnection;
    }

    public function setLastConnection(\DateTime $lastConnection): static
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
    * @see UserInterface
    */
   public function getSalt(): ?string
   {
       return null;
   }

   /**
    * @see UserInterface
    */
   public function eraseCredentials()
   {
       $this->plainPassword = null;
   }

   public function getUserIdentifier(): string
   {
       return $this->username;
   }
}