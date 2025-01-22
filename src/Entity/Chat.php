<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'chats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chat', orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isSupport = false;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Возвращает идентификатор чата
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает пользователя, связанного с чатом
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Устанавливает пользователя, связанного с чатом
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Возвращает коллекцию сообщений в чате
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    // Добавляет сообщение в чат
    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setChat($this);
        }
        return $this;
    }

    // Удаляет сообщение из чата
    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }
        return $this;
    }

    // Возвращает дату создания чата
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Устанавливает дату создания чата
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Проверяет, является ли чат чатом поддержки
    public function isSupport(): bool
    {
        return $this->isSupport;
    }

    // Устанавливает статус чата (поддержка или нет)
    public function setIsSupport(bool $isSupport): self
    {
        $this->isSupport = $isSupport;
        return $this;
    }

    public function setSupport(bool $isSupport): static
    {
        $this->isSupport = $isSupport;

        return $this;
    }
}