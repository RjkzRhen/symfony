<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'user')]
    private Collection $chats;

    #[ORM\OneToMany(targetEntity: Phone::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $phones;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $resetPasswordCode = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetPasswordCodeExpiry = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isTwoFactorEnabled = false;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $twoFactorCode = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $twoFactorCodeExpiry = null;

    public function __construct()
    {
        $this->chats = new ArrayCollection();
        $this->phones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = strtolower($username);
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setUser($this);
        }
        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->removeElement($chat)) {
            if ($chat->getUser() === $this) {
                $chat->setUser(null);
            }
        }
        return $this;
    }

    public function getResetPasswordCode(): ?string
    {
        return $this->resetPasswordCode;
    }

    public function setResetPasswordCode(?string $resetPasswordCode): self
    {
        $this->resetPasswordCode = $resetPasswordCode;
        return $this;
    }

    public function getResetPasswordCodeExpiry(): ?\DateTimeInterface
    {
        return $this->resetPasswordCodeExpiry;
    }

    public function setResetPasswordCodeExpiry(?\DateTimeInterface $resetPasswordCodeExpiry): self
    {
        $this->resetPasswordCodeExpiry = $resetPasswordCodeExpiry;
        return $this;
    }

    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->setUser($this);
        }
        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            if ($phone->getUser() === $this) {
                $phone->setUser(null);
            }
        }
        return $this;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setIsTwoFactorEnabled(bool $isTwoFactorEnabled): self
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;
        return $this;
    }

    public function getTwoFactorCode(): ?string
    {
        return $this->twoFactorCode;
    }

    public function setTwoFactorCode(?string $twoFactorCode): self
    {
        $this->twoFactorCode = $twoFactorCode;
        return $this;
    }

    public function getTwoFactorCodeExpiry(): ?\DateTimeInterface
    {
        return $this->twoFactorCodeExpiry;
    }

    public function setTwoFactorCodeExpiry(?\DateTimeInterface $twoFactorCodeExpiry): self
    {
        $this->twoFactorCodeExpiry = $twoFactorCodeExpiry;
        return $this;
    }
}