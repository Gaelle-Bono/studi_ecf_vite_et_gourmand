<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case PREPARING = 'PREPARING';
    case DELIVERING = 'DELIVERING';
    case DELIVERED = 'DELIVERED';
    case EQUIPMENT_RETURN = 'EQUIPMENT_RETURN';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Acceptée',
            self::PREPARING => 'En préparation',
            self::DELIVERING => 'En livraison',
            self::DELIVERED => 'Livrée',
            self::EQUIPMENT_RETURN => 'En attente du retour de matériel',
            self::COMPLETED => 'Terminée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function getDisplay(): array
    {
        return match ($this) {
            self::PENDING => [
                'text' => 'Commande en attente',
                'class' => 'alert-warning',
                'icon' => 'bi-hourglass-split',
            ],
            self::ACCEPTED => [
                'text' => 'Commande acceptée',
                'class' => 'alert-success',
                'icon' => 'bi-check-circle',
            ],
            self::PREPARING => [
                'text' => 'Commande en préparation',
                'class' => 'alert-info',
                'icon' => 'bi-gear',
            ],
            self::DELIVERING => [
                'text' => 'Commande en livraison',
                'class' => 'alert-primary',
                'icon' => 'bi-truck',
            ],
            self::DELIVERED => [
                'text' => 'Commande livrée',
                'class' => 'alert-success',
                'icon' => 'bi-check-circle-fill',
            ],
            self::EQUIPMENT_RETURN => [
                'text' => 'Commande en attente du retour de matériel',
                'class' => 'alert-warning',
                'icon' => 'bi-arrow-counterclockwise',
            ],
            self::COMPLETED => [
                'text' => 'Commande terminée',
                'class' => 'alert-success',
                'icon' => 'bi-check-circle-fill',
            ],
            self::CANCELLED => [
                'text' => 'Commande annulée',
                'class' => 'alert-danger',
                'icon' => 'bi-x-circle-fill',
            ]
        };
    }
}
