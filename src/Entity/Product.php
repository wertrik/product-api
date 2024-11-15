<?php

namespace App\Entity;

use App\DTO\ProductDTO;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false,)]
    private string $name;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $price;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;


    public function getId(): ?int
    {
        return $this->id;
    }

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


    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }


    /*
     * Před uložením produktu do DB nastaví created_at na aktuální čas
     */
    #[ORM\PrePersist]
    public function setCreatedAtToNow(): self
    {
        $this->created_at = new \DateTimeImmutable('now');
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /*
     * Před aktualizací produktu v DB nastaví updated_at na aktuální čas
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtToNow(): self
    {
        $this->updated_at = new \DateTime('now');
        return $this;
    }


    /*
     * Statická metoda pro vytvoření entity z DTO objektu
     */
    public static function createFromDTO(ProductDTO $productDTO): self
    {
        $product = new self();
        $product->name = $productDTO->getName();
        $product->price = $productDTO->getPrice();
        return $product;
    }

    public function changeFromDTO(ProductDTO $productDTO): self
    {
        $this->name = $productDTO->getName();
        $this->price = $productDTO->getPrice();
        return $this;
    }

}