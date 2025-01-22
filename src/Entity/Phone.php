<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "phones")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $value = null;

    private array $phones = [];

    // Возвращает идентификатор телефона
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает пользователя, связанного с телефоном
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Устанавливает пользователя, связанного с телефоном
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Возвращает номер телефона
    public function getValue(): ?string
    {
        return $this->value;
    }

    // Устанавливает номер телефона
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    // Возвращает массив телефонов (не используется в ORM)
    public function getPhones(): array
    {
        return $this->phones;
    }

    // Устанавливает массив телефонов (не используется в ORM)
    public function setPhones(array $phones): self
    {
        $this->phones = $phones;
        return $this;
    }
}
