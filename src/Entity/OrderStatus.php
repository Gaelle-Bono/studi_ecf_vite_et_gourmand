<?php

namespace App\Entity;

use App\Repository\OrderStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $code;

    #[ORM\Column(length: 100)]
    private string $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getStatusDisplay(): array
    {
        switch ($this->code) {
            case 'PENDING':
                return [
                    'class' => 'alert-warning',
                    'icon' => 'bi-hourglass-split',
                    'text' => 'Commande en attente'
                ];

            case 'CONFIRMED':
                return [
                    'class' => 'alert-success',
                    'icon' => 'bi-check-circle',
                    'text' => 'Commande acceptée'
                ];

            case 'PREPARING':
                return [
                    'class' => 'alert-info',
                    'icon' => 'bi-gear',
                    'text' => 'Commande en préparation'
                ];

            case 'READY':
                return [
                    'class' => 'alert-info',
                    'icon' => 'bi-check2-square',
                    'text' => 'Commande prête'
                ];

            case 'DELIVERING':
                return [
                    'class' => 'alert-primary',
                    'icon' => 'bi-truck',
                    'text' => 'Commande en livraison'
                ];

            case 'COMPLETED':
                return [
                    'class' => 'alert-success',
                    'icon' => 'bi-check-circle-fill',
                    'text' => 'Commande terminée'
                ];

            case 'CANCELLED':
                return [
                    'class' => 'alert-danger',
                    'icon' => 'bi-x-circle',
                    'text' => 'Commande annulée'
                ];

            default:
                return [
                    'class' => 'alert-secondary',
                    'icon' => 'bi-info-circle',
                    'text' => 'Statut de la commande inconnu'
                ];
        }
    }


}
