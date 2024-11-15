<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{


    #[Assert\NotBlank(message: 'Název produktu nesmí být prázdný')]
    #[Assert\Length(min: 1, max: 255)]
    #[Assert\Type(type: 'string')]
    private string $name;

    #[Assert\NotBlank(message: 'Cena produktu nesmí být prázdná')]
    #[Assert\Type(type: 'float')]
    private float $price;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

}