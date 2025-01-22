<?php

namespace App\Entity;

use App\Repository\EmployeeDirectoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeDirectoryRepository::class)]
class EmployeeDirectory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\Column]
    private ?int $telegramId = null;

    // Возвращает идентификатор сотрудника
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает фамилию сотрудника
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    // Устанавливает фамилию сотрудника
    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    // Возвращает имя сотрудника
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    // Устанавливает имя сотрудника
    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    // Возвращает отчество сотрудника
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    // Устанавливает отчество сотрудника
    public function setMiddleName(string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    // Возвращает должность сотрудника
    public function getPosition(): ?string
    {
        return $this->position;
    }

    // Устанавливает должность сотрудника
    public function setPosition(?string $position): static
    {
        $this->position = $position;

        return $this;
    }

    // Возвращает ID Telegram сотрудника
    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }

    // Устанавливает ID Telegram сотрудника
    public function setTelegramId(int $telegramId): static
    {
        $this->telegramId = $telegramId;

        return $this;
    }
}