<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class UserCsv
{
    #[Assert\NotBlank]
    public string $Last_Name;

    #[Assert\NotBlank]
    public string $First_Name;

    public ?string $Middle_Name = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 150)]
    public int $Age;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $Username;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $Password;
}