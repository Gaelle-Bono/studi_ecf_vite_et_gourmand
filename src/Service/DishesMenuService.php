<?php

namespace App\Service;
use App\Entity\Menu;
use App\Entity\DishType;


class DishesMenuService
{
    public function prepareDishesByType(Menu $menu): array
    {
        $dishesCategories = [
            'Entrées' => [
                'dishes' => [],
                'emptyText' => "Pas d’entrée dans ce menu",
            ],
            'Plats' => [
                'dishes' => [],
                'emptyText' => "Pas de plats dans ce menu",
            ],
            'Desserts' => [
                'dishes' => [],
                'emptyText' => "Pas de desserts dans ce menu",
            ],
        ];

        $allDishes = [];

        foreach ($menu->getDishes() as $dish) {
            $type = $dish->getDishType()->getName();
            $allDishes[] = $dish;

            if ($type === DishType::STARTER) {
                $dishesCategories['Entrées']['dishes'][] = $dish;
            } elseif ($type === DishType::MAIN) {
                $dishesCategories['Plats']['dishes'][] = $dish;
            } elseif ($type === DishType::DESSERT) {
                $dishesCategories['Desserts']['dishes'][] = $dish;
            }
        }

        return [
            'dishesCategories' => $dishesCategories,
            'allDishes' => $allDishes,
        ];
    }
}