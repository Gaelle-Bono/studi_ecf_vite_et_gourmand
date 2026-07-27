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
    private string $orderNumber;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    //Service details
    #[ORM\Column]
    private \DateTimeImmutable $requestedDeliveryAt;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deliveryAt = null;
    
////////////////// ADDRESS FIELDS /////////////////////

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $serviceAddress;
    
    #[ORM\Column(length: 180, nullable: true)]
     #[Assert\Length(
        max: 180,
        maxMessage: "Le complément d'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $serviceAddressComplement = null;


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


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $deliveryInstructionsAtOrder = null;



    ////////////////// LATITUDE, LONGITUDE AND DISTANCE (Company and delivery)/////////////////////

    #[ORM\Column(nullable: true)]
    private ?float $deliveryLatAtOrder = null;

    #[ORM\Column(nullable: true)]
    private ?float $deliveryLngAtOrder = null;

     #[ORM\Column(nullable: true)]
    private ?float $companyLatAtOrder = null;

    #[ORM\Column(nullable: true)]
    private ?float $companyLngAtOrder = null;


    #[ORM\Column(nullable: true)]
    private ?float $deliveryDistanceAtOrder = null;



    /////////////////MENU DETAILS //////////////////////////
    // at the time of order (to keep a record even if menu changes later)
    
    #[ORM\Column(length: 100)]
    private string $menuTitleAtOrder;

    #[ORM\Column(type: Types::TEXT)]
    private string $menuDescriptionAtOrder;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $starterTitleAtOrder = null;

    #[ORM\Column(length: 100)]
    private string $mainCourseTitleAtOrder;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dessertTitleAtOrder = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergensAtOrder = null;

    ///////////Nb people /////////////////////////
    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(
        value: 1,
        message: 'La commande doit être effectuée pour au moins 1 personne'
    )]
    private int $numberOfPeople;


    /////////////////////// Prices //////////////
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $pricePerPersonAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $servicePriceBeforeDiscountAtOrder = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $servicePriceAtOrder ='0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $deliveryPriceAtOrder ='0';
    
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalPriceAtOrder ='0';
    
    //////////////// equipment Loan //////////////
    #[ORM\Column(type: 'boolean')]
    private bool $requiresEquipmentLoanAtOrder = false;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $includedEquipmentDescriptionAtOrder = null;

    /////////////////////CUSTOMER DETAILS //////////////
    // at the time of order to keep a record even if customer updates their profile later
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

    // RELATIONSSHIPS 
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private OrderStatus $orderStatus;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Un menu est obligatoire.")]
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

    public function generateOrderNumber(): void
    {
        $this->orderNumber =
            'ORD-' . (new \DateTimeImmutable())->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
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

    public function getDeliveryInstructionsAtOrder(): ?string
    {
        return $this->deliveryInstructionsAtOrder;
    }

    public function setDeliveryInstructionsAtOrder(?string $deliveryInstructionsAtOrder): static
    {
        $this->deliveryInstructionsAtOrder = $deliveryInstructionsAtOrder;
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

    public function getFullServiceAddress(): string
    {
        return trim(
            $this->serviceAddress . ' ' .
            $this->serviceZipCode . ' ' .
            $this->serviceCity
        );
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



    public function getDeliveryLatAtOrder(): ?float
    {
        return $this->deliveryLatAtOrder;
    }

    public function setDeliveryLatAtOrder(?float $deliveryLatAtOrder): static
    {
        $this->deliveryLatAtOrder = $deliveryLatAtOrder;

        return $this;
    }

    public function getDeliveryLngAtOrder(): ?float
    {
        return $this->deliveryLngAtOrder;
    }

    public function setDeliveryLngAtOrder(?float $deliveryLngAtOrder): static
    {
        $this->deliveryLngAtOrder = $deliveryLngAtOrder;

        return $this;
    }

    public function getCompanyLatAtOrder(): ?float
    {
        return $this->companyLatAtOrder;
    }

    public function setCompanyLatAtOrder(?float $companyLatAtOrder): static
    {
        $this->companyLatAtOrder = $companyLatAtOrder;

        return $this;
    }

    public function getCompanyLngAtOrder(): ?float
    {
        return $this->companyLngAtOrder;
    }

    public function setCompanyLngAtOrder(?float $companyLngAtOrder): static
    {
        $this->companyLngAtOrder = $companyLngAtOrder;

        return $this;
    }

    public function getDeliveryDistanceAtOrder(): ?float
    {
        return $this->deliveryDistanceAtOrder;
    }

    public function setDeliveryDistanceAtOrder(?float $deliveryDistanceAtOrder): static
    {
        $this->deliveryDistanceAtOrder = $deliveryDistanceAtOrder;

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

    public function getStarterTitleAtOrder(): ?string
    {
        return $this->starterTitleAtOrder;
    }

    public function setStarterTitleAtOrder(?string $starterTitleAtOrder): static
    {
        $this->starterTitleAtOrder = $starterTitleAtOrder;
        return $this;
    }

    public function getMainCourseTitleAtOrder(): string
    {
        return $this->mainCourseTitleAtOrder;
    }

    public function setMainCourseTitleAtOrder(string $mainCourseTitleAtOrder): static
    {
        $this->mainCourseTitleAtOrder = $mainCourseTitleAtOrder;
        return $this;
    }

    public function getDessertTitleAtOrder(): ?string
    {
        return $this->dessertTitleAtOrder;
    }

    public function setDessertTitleAtOrder(?string $dessertTitleAtOrder): static
    {
        $this->dessertTitleAtOrder = $dessertTitleAtOrder;
        return $this;
    }

    public function getAllergensAtOrder(): ?string
    {
        return $this->allergensAtOrder;
    }

    public function setAllergensAtOrder(?string $allergensAtOrder): static
    {
        $this->allergensAtOrder = $allergensAtOrder;
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

    public function getServicePriceBeforeDiscountAtOrder(): string
    {
        return $this->servicePriceBeforeDiscountAtOrder;
    }

    public function setServicePriceBeforeDiscountAtOrder(string $servicePriceBeforeDiscountAtOrder): static
    {
        $this->servicePriceBeforeDiscountAtOrder = $servicePriceBeforeDiscountAtOrder;
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

    public function setTotalPriceAtOrder(string $totalPriceAtOrder): static
    {
        $this->totalPriceAtOrder = $totalPriceAtOrder;
        return $this;
    }

    public function isRequiresEquipmentLoanAtOrder(): bool
    {
        return $this->requiresEquipmentLoanAtOrder;
    }

    public function setRequiresEquipmentLoanAtOrder(bool $requiresEquipmentLoanAtOrder): static
    {
        $this->requiresEquipmentLoanAtOrder = $requiresEquipmentLoanAtOrder;
        return $this;
    }

    public function getIncludedEquipmentDescriptionAtOrder(): ?string
    {
        return $this->includedEquipmentDescriptionAtOrder;
    }

    public function setIncludedEquipmentDescriptionAtOrder(?string $includedEquipmentDescriptionAtOrder): static
    {
        $this->includedEquipmentDescriptionAtOrder = $includedEquipmentDescriptionAtOrder;
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

    public function getCustomerLastNameAtOrder(): string
    {
        return $this->customerLastNameAtOrder;
    }

    public function setCustomerLastNameAtOrder(string $customerLastNameAtOrder): static
    {
        $this->customerLastNameAtOrder = $customerLastNameAtOrder;
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