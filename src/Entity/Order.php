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
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $orderNumber;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(1)]
    private int $numberOfPeople;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $requestedDeliveryAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deliveryAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $serviceAddress = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private string $servicePriceAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private string $deliveryPriceAtOrder;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalPriceAtOrder;

    #[ORM\Column]
    private bool $equipmentLoan = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $menuTitleAtOrder = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $menuDescriptionAtOrder = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private string $pricePerPersonAtOrder;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private OrderStatus $orderStatus;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
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

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
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

    public function getRequestedDeliveryAt(): ?\DateTimeImmutable
    {
        return $this->requestedDeliveryAt;
    }

    public function setRequestedDeliveryAt(?\DateTimeImmutable $requestedDeliveryAt): static
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

    public function getNumberOfPeople(): int
    {
        return $this->numberOfPeople;
    }

    public function setNumberOfPeople(int $numberOfPeople): static
    {
        $this->numberOfPeople = $numberOfPeople;
        return $this;
    }

    public function getServiceAddress(): ?string
    {
        return $this->serviceAddress;
    }

    public function setServiceAddress(?string $serviceAddress): static
    {
        $this->serviceAddress = $serviceAddress;
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

    public function isEquipmentLoan(): bool
    {
        return $this->equipmentLoan;
    }

    public function setEquipmentLoan(bool $equipmentLoan): static
    {
        $this->equipmentLoan = $equipmentLoan;
        return $this;
    }

    public function getMenuTitleAtOrder(): ?string
    {
        return $this->menuTitleAtOrder;
    }

    public function setMenuTitleAtOrder(?string $menuTitleAtOrder): static
    {
        $this->menuTitleAtOrder = $menuTitleAtOrder;
        return $this;
    }

    public function getMenuDescriptionAtOrder(): ?string
    {
        return $this->menuDescriptionAtOrder;
    }

    public function setMenuDescriptionAtOrder(?string $menuDescriptionAtOrder): static
    {
        $this->menuDescriptionAtOrder = $menuDescriptionAtOrder;

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