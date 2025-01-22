<?php

namespace App\Entity;

use App\Repository\ArrivalJournalDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArrivalJournalDetailRepository::class)]
class ArrivalJournalDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ArrivalJournal::class, inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArrivalJournal $arrivalJournal = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\ManyToOne(targetEntity: ExternalRate::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExternalRate $externalRate = null;


    #[ORM\ManyToOne(targetEntity: Counterparty::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Counterparty $counterparty = null;

    #[ORM\ManyToOne(targetEntity: Unit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $unit = null;

    #[ORM\Column(type: 'float')]
    private ?float $value = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    // Геттеры и сеттеры

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrivalJournal(): ?ArrivalJournal
    {
        return $this->arrivalJournal;
    }

    public function setArrivalJournal(?ArrivalJournal $arrivalJournal): self
    {
        $this->arrivalJournal = $arrivalJournal;
        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getExternalRate(): ?ExternalRate
    {
        return $this->externalRate;
    }

    public function setExternalRate(?ExternalRate $externalRate): self
    {
        $this->externalRate = $externalRate;
        return $this;
    }

    public function getCounterparty(): ?Counterparty
    {
        return $this->counterparty;
    }

    public function setCounterparty(?Counterparty $counterparty): self
    {
        $this->counterparty = $counterparty;
        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;
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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
}