<?php
// src/Entity/Message.php
namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Chat::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chat $chat = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Возвращает идентификатор сообщения
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает чат, к которому относится сообщение
    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    // Устанавливает чат, к которому относится сообщение
    public function setChat(?Chat $chat): self
    {
        $this->chat = $chat;
        return $this;
    }

    // Возвращает отправителя сообщения
    public function getSender(): ?User
    {
        return $this->sender;
    }

    // Устанавливает отправителя сообщения
    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    // Возвращает содержание сообщения
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Устанавливает содержание сообщения
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    // Возвращает дату создания сообщения
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Устанавливает дату создания сообщения
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}