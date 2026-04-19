<?php

namespace App\Service;
use App\Entity\Menu;
use App\Constant\AppConstant;


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

            if ($type === AppConstant::STARTER) {
                $dishesCategories['Entrées']['dishes'][] = $dish;
            } elseif ($type === AppConstant::MAIN) {
                $dishesCategories['Plats']['dishes'][] = $dish;
            } elseif ($type === AppConstant::DESSERT) {
                $dishesCategories['Desserts']['dishes'][] = $dish;
            }
        }

        return [
            'dishesCategories' => $dishesCategories,
            'allDishes' => $allDishes,
        ];
    }
}