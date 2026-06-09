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
                'message' => "Ce menu est en rupture de stock : la commande est impossible",
                'icon' => 'bi bi-x-circle',
            ];
        }

        if ($menu->getRemainingQuantity() < $menu->getMinimumNumberOfPeople()) {
            return [
                'type' => 'warning',
                'message' => "Le stock est insuffisant : ce menu ne peut pas être commandé actuellement",
                'icon' => 'bi bi-exclamation-triangle',
            ];
        }

        //everything is ok
        return null;
    }

}