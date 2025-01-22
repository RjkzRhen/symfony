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

    // Возвращает идентификатор квартиры
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает имя владельца квартиры
    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    // Устанавливает имя владельца квартиры
    public function setOwnerName(?string $ownerName): self
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    // Возвращает номер телефона владельца квартиры
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    // Устанавливает номер телефона владельца квартиры
    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    // Возвращает номер домофона квартиры
    public function getIntercomNumber(): ?string
    {
        return $this->intercomNumber;
    }

    // Устанавливает номер домофона квартиры
    public function setIntercomNumber(?string $intercomNumber): self
    {
        $this->intercomNumber = $intercomNumber;

        return $this;
    }

    // Возвращает количество жильцов в квартире
    public function getResidentsCount(): ?int
    {
        return $this->residentsCount;
    }

    // Устанавливает количество жильцов в квартире
    public function setResidentsCount(?int $residentsCount): self
    {
        $this->residentsCount = $residentsCount;

        return $this;
    }

    // Возвращает количество комнат в квартире
    public function getRoomsCount(): ?int
    {
        return $this->roomsCount;
    }

    // Устанавливает количество комнат в квартире
    public function setRoomsCount(?int $roomsCount): self
    {
        $this->roomsCount = $roomsCount;

        return $this;
    }

    // Возвращает номер квартиры
    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    // Устанавливает номер квартиры
    public function setApartmentNumber(string $apartmentNumber): self
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }
}