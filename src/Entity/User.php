<?php

namespace App\Entity; // Определяем пространство имен для сущности

use App\Repository\UserRepository; // Подключаем репозиторий UserRepository
use Doctrine\ORM\Mapping as ORM; // Подключаем аннотации ORM для маппинга сущности

#[ORM\Entity(repositoryClass: UserRepository::class)] // Определяем сущность и связываем её с репозиторием UserRepository
class User // Определяем класс сущности User
{
    #[ORM\Id] // Определяем поле как первичный ключ
    #[ORM\GeneratedValue] // Указываем, что значение будет генерироваться автоматически
    #[ORM\Column] // Определяем поле как столбец в таблице
    private ?int $id = null; // Определяем свойство id с типом int и значением по умолчанию null

    #[ORM\Column(length: 255)] // Определяем поле как столбец в таблице с длиной 255 символов
    private ?string $lastName = null; // Определяем свойство lastName с типом string и значением по умолчанию null

    #[ORM\Column(length: 255)] // Определяем поле как столбец в таблице с длиной 255 символов
    private ?string $firstName = null; // Определяем свойство firstName с типом string и значением по умолчанию null

    #[ORM\Column(length: 255)] // Определяем поле как столбец в таблице с длиной 255 символов
    private ?string $middleName = null; // Определяем свойство middleName с типом string и значением по умолчанию null

    #[ORM\Column(type: "integer")] // Определяем поле как столбец в таблице с типом integer
    private ?int $age = null; // Определяем свойство age с типом int и значением по умолчанию null

    #[ORM\Column(length: 255)] // Определяем поле как столбец в таблице с длиной 255 символов
    private ?string $username = null; // Определяем свойство username с типом string и значением по умолчанию null

    #[ORM\Column(length: 255)] // Определяем поле как столбец в таблице с длиной 255 символов
    private ?string $password = null; // Определяем свойство password с типом string и значением по умолчанию null

    public function getId(): ?int // Определяем метод для получения значения свойства id
    {
        return $this->id; // Возвращаем значение свойства id
    }

    public function getLastName(): ?string // Определяем метод для получения значения свойства lastName
    {
        return $this->lastName; // Возвращаем значение свойства lastName
    }

    public function setLastName(string $lastName): static // Определяем метод для установки значения свойства lastName
    {
        $this->lastName = $lastName; // Устанавливаем значение свойства lastName

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

    public function getFirstName(): ?string // Определяем метод для получения значения свойства firstName
    {
        return $this->firstName; // Возвращаем значение свойства firstName
    }

    public function setFirstName(string $firstName): static // Определяем метод для установки значения свойства firstName
    {
        $this->firstName = $firstName; // Устанавливаем значение свойства firstName

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

    public function getMiddleName(): ?string // Определяем метод для получения значения свойства middleName
    {
        return $this->middleName; // Возвращаем значение свойства middleName
    }

    public function setMiddleName(string $middleName): static // Определяем метод для установки значения свойства middleName
    {
        $this->middleName = $middleName; // Устанавливаем значение свойства middleName

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

    public function getAge(): ?int // Определяем метод для получения значения свойства age
    {
        return $this->age; // Возвращаем значение свойства age
    }

    public function setAge(int $age): self // Определяем метод для установки значения свойства age
    {
        $this->age = $age; // Устанавливаем значение свойства age

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

    public function getUsername(): ?string // Определяем метод для получения значения свойства username
    {
        return $this->username; // Возвращаем значение свойства username
    }

    public function setUsername(string $username): static // Определяем метод для установки значения свойства username
    {
        $this->username = $username; // Устанавливаем значение свойства username

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

    public function getPassword(): ?string // Определяем метод для получения значения свойства password
    {
        return $this->password; // Возвращаем значение свойства password
    }

    public function setPassword(string $password): static // Определяем метод для установки значения свойства password
    {
        $this->password = $password; // Устанавливаем значение свойства password

        return $this; // Возвращаем текущий объект для возможности цепочки вызовов
    }

}