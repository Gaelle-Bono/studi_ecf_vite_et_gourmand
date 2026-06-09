<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\Order;

class OrderValidationService
{
    public function validate(Order $order, Menu $menu, \DateTimeImmutable $serviceDate): ?string
    {
        $today = new \DateTimeImmutable('today');
        $minimumDays = $menu->getMinimumDaysBeforeOrder();
        $numberOfPeople = $order->getNumberOfPeople();

        //Date validation : service date must be at least X days after order date, X being defined in menu details
        if ($minimumDays !== null) {
            $minimumDate = $today->modify("+{$minimumDays} days");

            if ($serviceDate < $minimumDate) {
                return "Ce menu doit être commandé au moins {$minimumDays} jours à l’avance";
            }
        }

        //number of people validation : cannot be less than menu minimum nor more than remaining quantity
        if ($numberOfPeople < $menu->getMinimumNumberOfPeople()) {
            return 'Le nombre de personnes est inférieur au minimum requis pour ce menu';
        }

        // validation stock
        if ($numberOfPeople > $menu->getRemainingQuantity()) {
            return 'Stock insuffisant pour ce nombre de personnes';
        }

        return null; // OK
    }
}