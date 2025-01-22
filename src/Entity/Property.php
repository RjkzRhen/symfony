<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[Vich\Uploadable]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $rooms = null;

    #[ORM\Column(length: 255)]
    private ?string $area = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null; // Добавлено поле address

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'property_images', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Возвращает идентификатор объекта недвижимости
    public function getId(): ?int
    {
        return $this->id;
    }

    // Возвращает название объекта недвижимости
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Устанавливает название объекта недвижимости
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Возвращает описание объекта недвижимости
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Устанавливает описание объекта недвижимости
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Возвращает цену объекта недвижимости
    public function getPrice(): ?string
    {
        return $this->price;
    }

    // Устанавливает цену объекта недвижимости
    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    // Возвращает количество комнат в объекте недвижимости
    public function getRooms(): ?string
    {
        return $this->rooms;
    }

    // Устанавливает количество комнат в объекте недвижимости
    public function setRooms(string $rooms): static
    {
        $this->rooms = $rooms;
        return $this;
    }

    // Возвращает площадь объекта недвижимости
    public function getArea(): ?string
    {
        return $this->area;
    }

    // Устанавливает площадь объекта недвижимости
    public function setArea(string $area): static
    {
        $this->area = $area;
        return $this;
    }

    // Возвращает адрес объекта недвижимости
    public function getAddress(): ?string
    {
        return $this->address;
    }

    // Устанавливает адрес объекта недвижимости
    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    // Возвращает путь к изображению объекта недвижимости
    public function getImage(): ?string
    {
        return $this->image;
    }

    // Устанавливает путь к изображению объекта недвижимости
    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    // Возвращает файл изображения объекта недвижимости
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    // Устанавливает файл изображения объекта недвижимости
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    // Возвращает дату последнего обновления объекта недвижимости
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Устанавливает дату последнего обновления объекта недвижимости
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Возвращает строковое представление объекта недвижимости (название)
    public function __toString(): string
    {
        return $this->title ?? 'Новая квартира';
    }
}