<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $title;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(1)]
    private int $minimumNumberOfPeople = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private string $pricePerPerson;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    private string $description;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $remainingQuantity;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('array')]
    private ?array $conditions = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Diet $diet;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Theme $theme;

    //relations
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dish $starter = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Dish $main;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dish $dessert = null;


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

    public function getMinimumNumberOfPeople(): int
    {
        return $this->minimumNumberOfPeople;
    }

    public function setMinimumNumberOfPeople(int $minimumNumberOfPeople): static
    {
        $this->minimumNumberOfPeople = $minimumNumberOfPeople;

        return $this;
    }

    public function getPricePerPerson(): string
    {
        return $this->pricePerPerson;
    }

    public function getTotalPriceForMinimumPeople(): string
    {
        return bcmul($this->pricePerPerson, (string) $this->minimumNumberOfPeople, 2);
    }


    public function setPricePerPerson(string $pricePerPerson): static
    {
        $this->pricePerPerson = $pricePerPerson;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRemainingQuantity(): int
    {
        return $this->remainingQuantity;
    }

    public function setRemainingQuantity(int $remainingQuantity): static
    {
        $this->remainingQuantity = $remainingQuantity;

        return $this;
    }

    public function getConditions(): ?array
    {
        return $this->conditions;
    }

    public function setConditions(?array $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getDiet(): Diet
    {
        return $this->diet;
    }

    public function setDiet(Diet $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getStarter(): ?Dish
    {
        return $this->starter;
    }

    public function setStarter(?Dish $starter): static
    {
        $this->starter = $starter;

        return $this;
    }

    public function getMain(): Dish
    {
        return $this->main;
    }

    public function setMain(Dish $main): static
    {
        $this->main = $main;

        return $this;
    }

    public function getDessert(): ?Dish
    {
        return $this->dessert;
    }

    public function setDessert(?Dish $dessert): static
    {
        $this->dessert = $dessert;

        return $this;
    }

}
