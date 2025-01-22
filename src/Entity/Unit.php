<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Unit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $code = null;

    // Возвращает ID единицы измерения
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает название единицы измерения
    public function getName(): ?string
    {
        return $this->name;
    }

    // Устанавливает название единицы измерения
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Возвращает код единицы измерения
    public function getCode(): ?string
    {
        return $this->code;
    }

    // Устанавливает код единицы измерения
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
}