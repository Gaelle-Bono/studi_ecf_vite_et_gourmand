<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $title;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    /**
     * @var Collection<int, Allergen>
     */
    #[ORM\ManyToMany(targetEntity: Allergen::class)]
    private Collection $allergens;

    public function __construct()
    {
        $this->allergens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }


    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): static
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * @return Collection<int, Allergen>
     */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function addAllergen(Allergen $allergen): static
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens->add($allergen);
        }

        return $this;
    }

    public function removeAllergen(Allergen $allergen): static
    {
        $this->allergens->removeElement($allergen);

        return $this;
    }

}
