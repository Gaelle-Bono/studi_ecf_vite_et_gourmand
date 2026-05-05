<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\Length(max: 50)]
    #[Assert\Regex("/^[A-Z0-9\-]+$/")]
    private string $orderNumber;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private \DateTimeImmutable $requestedDeliveryAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deliveryAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $serviceAddress;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        message: "Le code postal doit contenir uniquement des chiffres"
    )]
    #[Assert\Length(
        max: 10,
        maxMessage: "Le code postal ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $serviceZipCode;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "La ville doit contenir au moins {{ limit }} caractères",
        maxMessage: "La ville ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $serviceCity;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le pays doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le pays ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $serviceCountry;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(max: 180)]
    private ?string $serviceAddressComplement = null;
    
    // Storing menu details at the time of order to keep a record even if menu changes later
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $menuTitleAtOrder;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    private string $menuDescriptionAtOrder;

    #[ORM\Column(type: 'json')]
    private array $menuDishesAtOrder = [];
    
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $menuPriceAtOrder;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(1)]
    private int $numberOfPeople;

    //Prices at the time of order to keep a record even if prices change later
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $pricePerPersonAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $servicePriceAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $deliveryPriceAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalPriceAtOrder;

    #[ORM\Column]
    private bool $equipmentLoan = false;

    //Storing customer details at the time of order to keep a record even if customer updates their profile later
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ' -]+$/",
        message: "Le nom contient des caractères invalides"
    )]
    private string $customerLastNameAtOrder;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ' -]+$/",
        message: "Le prénom contient des caractères invalides"
    )]
    private string $customerFirstNameAtOrder;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(max: 180)]
    private string $customerEmailAtOrder;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire")]
    #[Assert\Regex(
    pattern: "/^\+?[0-9\s]{10,20}$/",
    message: "Le numéro de téléphone est invalide"
    )]
    private string $customerPhoneAtOrder;

    // Relationships
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private OrderStatus $orderStatus;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Menu $menu;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getRequestedDeliveryAt(): \DateTimeImmutable
    {
        return $this->requestedDeliveryAt;
    }

    public function setRequestedDeliveryAt(\DateTimeImmutable $requestedDeliveryAt): static
    {
        $this->requestedDeliveryAt = $requestedDeliveryAt;
        return $this;
    }

    public function getDeliveryAt(): ?\DateTimeImmutable
    {
        return $this->deliveryAt;
    }

    public function setDeliveryAt(?\DateTimeImmutable $deliveryAt): static
    {
        $this->deliveryAt = $deliveryAt;
        return $this;
    }

    public function getServiceAddress(): string
    {
        return $this->serviceAddress;
    }

    public function setServiceAddress(string $serviceAddress): static
    {
        $this->serviceAddress = $serviceAddress;
        return $this;
    }

    public function getServiceZipCode(): string
    {
        return $this->serviceZipCode;
    }

    public function setServiceZipCode(string $serviceZipCode): static
    {
        $this->serviceZipCode = $serviceZipCode;
        return $this;
    }

    public function getServiceCity(): string
    {
        return $this->serviceCity;
    }

    public function setServiceCity(string $serviceCity): static
    {
        $this->serviceCity = $serviceCity;
        return $this;
    }

    public function getServiceCountry(): string
    {
        return $this->serviceCountry;
    }
    public function setServiceCountry(string $serviceCountry): static
    {        
        $this->serviceCountry = $serviceCountry;
        return $this;
    }   

    public function getServiceAddressComplement(): ?string
    {
        return $this->serviceAddressComplement;
    }

    public function setServiceAddressComplement(?string $serviceAddressComplement): static
    {
        $this->serviceAddressComplement = $serviceAddressComplement;
        return $this;
    }

      public function getMenuTitleAtOrder(): string
    {
        return $this->menuTitleAtOrder;
    }

    public function setMenuTitleAtOrder(string $menuTitleAtOrder): static
    {
        $this->menuTitleAtOrder = $menuTitleAtOrder;
        return $this;
    }

    public function getMenuDescriptionAtOrder(): string
    {
        return $this->menuDescriptionAtOrder;
    }

    public function setMenuDescriptionAtOrder(string $menuDescriptionAtOrder): static
    {
        $this->menuDescriptionAtOrder = $menuDescriptionAtOrder;
        return $this;
    }

    public function getMenuDishesAtOrder(): array
    {
        return $this->menuDishesAtOrder;
    }

    public function setMenuDishesAtOrder(array $menuDishesAtOrder): static
    {
        $this->menuDishesAtOrder = $menuDishesAtOrder;
        return $this;
    }   

    public function getMenuPriceAtOrder(): string
    {
        return $this->menuPriceAtOrder;
    }

    public function setMenuPriceAtOrder(string $menuPriceAtOrder): static
    {
        $this->menuPriceAtOrder = $menuPriceAtOrder;
        return $this;
    }

    public function getNumberOfPeople(): int
    {
        return $this->numberOfPeople;
    }

    public function setNumberOfPeople(int $numberOfPeople): static
    {
        $this->numberOfPeople = $numberOfPeople;
        return $this;
    }

     public function getPricePerPersonAtOrder(): string
    {
        return $this->pricePerPersonAtOrder;
    }

    public function setPricePerPersonAtOrder(string $pricePerPersonAtOrder): static
    {
        $this->pricePerPersonAtOrder = $pricePerPersonAtOrder;
        return $this;
    }

    public function getServicePriceAtOrder(): string
    {
        return $this->servicePriceAtOrder;
    }

    public function setServicePriceAtOrder(string $servicePriceAtOrder): static
    {
        $this->servicePriceAtOrder = $servicePriceAtOrder;
        return $this;
    }

    public function getDeliveryPriceAtOrder(): string
    {
        return $this->deliveryPriceAtOrder;
    }

    public function setDeliveryPriceAtOrder(string $deliveryPriceAtOrder): static
    {
        $this->deliveryPriceAtOrder = $deliveryPriceAtOrder;
        return $this;
    }

    public function getTotalPriceAtOrder(): string
    {
        return $this->totalPriceAtOrder;
    }

    public function setTotalPriceAtOrder(string $total): static
    {
        $this->totalPriceAtOrder = $total;
        return $this;
    }

    public function isEquipmentLoan(): bool
    {
        return $this->equipmentLoan;
    }

    public function setEquipmentLoan(bool $equipmentLoan): static
    {
        $this->equipmentLoan = $equipmentLoan;
        return $this;
    }

    public function getCustomerLastNameAtOrder(): string
    {
        return $this->customerLastNameAtOrder;
    }

    public function setCustomerLastNameAtOrder(string $customerLastNameAtOrder): static
    {
        $this->customerLastNameAtOrder = $customerLastNameAtOrder;
        return $this;
    }

    public function getCustomerFirstNameAtOrder(): string
    {
        return $this->customerFirstNameAtOrder;
    }

    public function setCustomerFirstNameAtOrder(string $customerFirstNameAtOrder): static
    {
        $this->customerFirstNameAtOrder = $customerFirstNameAtOrder;
        return $this;
    }

    public function getCustomerEmailAtOrder(): string
    {
        return $this->customerEmailAtOrder;
    }

    public function setCustomerEmailAtOrder(string $customerEmailAtOrder): static
    {
        $this->customerEmailAtOrder = $customerEmailAtOrder;
        return $this;
    }

    public function getCustomerPhoneAtOrder(): string
    {
        return $this->customerPhoneAtOrder;
    }

    public function setCustomerPhoneAtOrder(string $customerPhoneAtOrder): static
    {
        $this->customerPhoneAtOrder = $customerPhoneAtOrder;
        return $this;
    }

    // Relationships getters/setters
    public function getOrderStatus(): OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }

    public function setMenu(Menu $menu): static
    {
        $this->menu = $menu;
        return $this;
    }

}