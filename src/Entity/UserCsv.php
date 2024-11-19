<?php

namespace App\Entity; // Определяем пространство имен для сущности

use Symfony\Component\Validator\Constraints as Assert; // Подключаем аннотации валидации

class UserCsv // Определяем класс сущности UserCsv
{
    #[Assert\NotBlank] // Указываем, что поле не должно быть пустым
    public string $lastName; // Определяем публичное свойство lastName с типом string

    #[Assert\NotBlank] // Указываем, что поле не должно быть пустым
    public string $firstName; // Определяем публичное свойство firstName с типом string

    public ?string $middleName = null; // Определяем публичное свойство middleName с типом string и значением по умолчанию null

    #[Assert\NotBlank] // Указываем, что поле не должно быть пустым
    #[Assert\Range(min: 1, max: 150)] // Указываем диапазон допустимых значений для поля
    public int $age; // Определяем публичное свойство age с типом int

    #[Assert\NotBlank] // Указываем, что поле не должно быть пустым
    #[Assert\Length(max: 255)] // Указываем максимальную длину строки
    public string $username; // Определяем публичное свойство username с типом string

    #[Assert\NotBlank] // Указываем, что поле не должно быть пустым
    #[Assert\Length(min: 6)] // Указываем минимальную длину строки
    public string $password; // Определяем публичное свойство password с типом string
}