<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $tax_rate = null;

    // Возвращает идентификатор настройки
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает налоговую ставку
    public function getTaxRate(): ?string
    {
        return $this->tax_rate;
    }

    // Устанавливает налоговую ставку
    public function setTaxRate(string $tax_rate): static
    {
        $this->tax_rate = $tax_rate;

        return $this;
    }
}