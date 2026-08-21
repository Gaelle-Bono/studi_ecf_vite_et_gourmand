<?php

namespace App\Entity;

use App\Repository\OrderCancellationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\ContactMethod;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderCancellationRepository::class)]
class OrderCancellation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $cancelledBy;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(
        groups: ['admin', 'employee'],
        message: "Le motif pour l'annulation est obligatoire"
    )]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "Le motif doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le motif ne peut pas dépasser {{ limit }} caractères",
        groups: ['admin', 'employee']
    )]
    private ?string $reason = null;

    #[ORM\Column(enumType: ContactMethod::class, nullable:true)]
    #[Assert\NotNull(
        groups: ['employee', 'admin'],
        message: "Le mode de contact est obligatoire"
    )]
    private ?ContactMethod $contactMethod = null;

    public function __construct(Order $order, User $cancelledBy) 
    {
        $this->order = $order;
        $this->cancelledBy = $cancelledBy;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCancelledBy(): User
    {
        return $this->cancelledBy;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getContactMethod(): ?ContactMethod
    {
        return $this->contactMethod;
    }

    public function setContactMethod(?ContactMethod $contactMethod): static
    {
        $this->contactMethod = $contactMethod;
        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

}
