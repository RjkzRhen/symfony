<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExternalRateRepository::class)]
class ExternalRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $employeeId;

    #[ORM\Column(type: 'date')]
    private $startDate;

    #[ORM\Column(type: 'date', nullable: true)]
    private $endDate;

    #[ORM\Column(type: 'float')]
    private $value;

    #[ORM\Column(type: 'integer')]
    private $counterpartyId;

    // Геттеры и сеттеры
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(int $employeeId): self
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getCounterpartyId(): ?int
    {
        return $this->counterpartyId;
    }

    public function setCounterpartyId(int $counterpartyId): self
    {
        $this->counterpartyId = $counterpartyId;
        return $this;
    }
}