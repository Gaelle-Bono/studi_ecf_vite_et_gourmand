<?php

namespace App\Service;

use App\Entity\Menu;

class StockMenuService
{

    public function getStockAlert(Menu $menu): ?array
    {
        if ($menu->getRemainingQuantity() === 0) {
            return [
                'type' => 'danger',
                'message' => "Ce menu n’est plus disponible (rupture de stock)",
                'icon' => 'bi bi-x-circle',
            ];
        }

        if ($menu->getRemainingQuantity() < $menu->getMinimumNumberOfPeople()) {
            return [
                'type' => 'warning',
                'message' => "Attention : quantité insuffisante pour une réservation minimale",
                'icon' => 'bi bi-exclamation-triangle',
            ];
        }

        //everything is ok
        return null;
    }

}