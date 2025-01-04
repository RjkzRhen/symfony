<?php

namespace App\Entity;

use App\Repository\ApartmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApartmentRepository::class)]
class Apartment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ownerName;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phoneNumber;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $intercomNumber;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $residentsCount;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $roomsCount;

    #[ORM\Column(type: 'string', length: 10)]
    private $apartmentNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(?string $ownerName): self
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIntercomNumber(): ?string
    {
        return $this->intercomNumber;
    }

    public function setIntercomNumber(?string $intercomNumber): self
    {
        $this->intercomNumber = $intercomNumber;

        return $this;
    }

    public function getResidentsCount(): ?int
    {
        return $this->residentsCount;
    }

    public function setResidentsCount(?int $residentsCount): self
    {
        $this->residentsCount = $residentsCount;

        return $this;
    }

    public function getRoomsCount(): ?int
    {
        return $this->roomsCount;
    }

    public function setRoomsCount(?int $roomsCount): self
    {
        $this->roomsCount = $roomsCount;

        return $this;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(string $apartmentNumber): self
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }
}