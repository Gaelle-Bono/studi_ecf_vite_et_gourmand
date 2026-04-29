<?php

namespace App\Entity;

use App\Repository\OrderStatusHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusHistoryRepository::class)]
final class OrderStatusHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $changedAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $changedBy;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private OrderStatus $orderStatus;


    public function __construct(User $changedBy, Order $order, OrderStatus $orderStatus) 
    {
        $this->changedAt = new \DateTimeImmutable();
        $this->changedBy = $changedBy;
        $this->order = $order;
        $this->orderStatus = $orderStatus;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChangedBy(): User
    {
        return $this->changedBy;
    }

    public function getChangedAt(): \DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getOrderStatus(): OrderStatus
    {
        return $this->orderStatus;
    }

}
