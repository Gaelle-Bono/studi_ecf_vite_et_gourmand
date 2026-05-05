<?php

namespace App\Enum;

enum DishType: string
{
    case STARTER = 'starter';
    case MAIN = 'main';
    case DESSERT = 'dessert';

    public function label(): string
    {
        return [
            'starter' => 'Entrée',
            'main' => 'Plat',
            'dessert' => 'Dessert',
        ][$this->value] ?? $this->value;
    }

}
