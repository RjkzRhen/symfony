<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class UserCsv
{
    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\NotBlank]
    public string $firstName;

    public ?string $middleName = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 150)]
    public int $age;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
