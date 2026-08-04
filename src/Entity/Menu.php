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

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom du menu est obligatoire")]
    #[Assert\Length(
        max: 100,
        maxMessage: "Le nom du menu ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description du menu est obligatoire")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description du menu ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $description;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: "Le menu doit être commandé pour au moins 1 personne"
    )]
    private int $minimumNumberOfPeople = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\Positive(message: "Le prix par personne doit être positif")]
    private string $pricePerPerson;

    #[ORM\Column]
    #[Assert\PositiveOrZero(
        message: "La quantité restante doit être supérieure ou égale à zéro"
    )]
    private int $remainingQuantity;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('array')]
    private ?array $conditions = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: "Ce menu doit être commandé au moins 1 jour à l’avance."
    )]
    private ?int $minimumDaysBeforeOrder = null;

    #[ORM\Column(type: 'boolean')]
    private bool $requiresEquipmentLoan = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $includedEquipmentDescription = null;

    //relations
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dish $starter = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Dish $mainCourse;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dish $dessert = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Diet $diet;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Theme $theme;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
    
    public function setPricePerPerson(string $pricePerPerson): static
    {
        $this->pricePerPerson = $pricePerPerson;

        return $this;
    }
    public function getTotalPriceForMinimumPeople(): string
    {
        return bcmul($this->pricePerPerson, (string) $this->minimumNumberOfPeople, 2);
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

    public function getMinimumDaysBeforeOrder(): ?int
    {
        return $this->minimumDaysBeforeOrder;
    }

    public function setMinimumDaysBeforeOrder(?int $minimumDaysBeforeOrder): static
    {
        $this->minimumDaysBeforeOrder = $minimumDaysBeforeOrder;

        return $this;
    }

    public function requiresEquipmentLoan(): bool
    {
        return $this->requiresEquipmentLoan;
    }

    public function setRequiresEquipmentLoan(bool $requiresEquipmentLoan): static
    {
        $this->requiresEquipmentLoan = $requiresEquipmentLoan;

        return $this;
    }

    public function getIncludedEquipmentDescription(): ?string
    {
        return $this->includedEquipmentDescription;
    }

    public function setIncludedEquipmentDescription(?string $includedEquipmentDescription): static
    {
        $this->includedEquipmentDescription = $includedEquipmentDescription;

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

    public function getMainCourse(): Dish
    {
        return $this->mainCourse;
    }

    public function setMainCourse(Dish $mainCourse): static
    {
        $this->mainCourse = $mainCourse;

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

}
