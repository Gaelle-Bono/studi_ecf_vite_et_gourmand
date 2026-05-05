<?php

namespace App\Service;

use App\Entity\Menu;
use App\Enum\DishType;

class OrderMenuFormatter
{
    public function group(Menu $menu): array
    {
        $grouped = [
            DishType::STARTER->value => [],
            DishType::MAIN->value => [],
            DishType::DESSERT->value => [],
        ];

        foreach ($menu->getDishes() as $dish) {
            $grouped[$dish->getDishType()->value][] = $dish;
        }

        return $grouped;
    }
}