<?php

// src/Entity/SupportMessage.php
namespace App\Entity;

use App\Repository\SupportMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportMessageRepository::class)]
class SupportMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $response = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Возвращает идентификатор сообщения поддержки
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает пользователя, отправившего сообщение
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Устанавливает пользователя, отправившего сообщение
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Возвращает текст сообщения поддержки
    public function getMessage(): ?string
    {
        return $this->message;
    }

    // Устанавливает текст сообщения поддержки
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    // Возвращает ответ на сообщение поддержки
    public function getResponse(): ?string
    {
        return $this->response;
    }

    // Устанавливает ответ на сообщение поддержки
    public function setResponse(?string $response): self
    {
        $this->response = $response;
        return $this;
    }

    // Возвращает дату создания сообщения поддержки
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Устанавливает дату создания сообщения поддержки
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}