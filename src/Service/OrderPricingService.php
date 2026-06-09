<?php

namespace App\Service;

use App\Entity\Menu;
use App\Constant\AppConstant;


class OrderPricingService
{

    public function calculatePricesForOrder(Menu $menu, int $numberOfPeople, float $distance): array
    {
        $servicePriceData = $this->calculateServicePriceData($menu, $numberOfPeople);
        $deliveryPrice = $this->calculateDeliveryPrice($distance);
        $totalPrice = $this->calculateTotalPrice($servicePriceData['servicePrice'], $deliveryPrice);

        return [
            'servicePriceBeforeDiscount' => $servicePriceData['servicePriceBeforeDiscount'],
            'hasDiscount' => $servicePriceData['hasDiscount'],
            'servicePrice' => $servicePriceData['servicePrice'],
            'discountAmount' => $servicePriceData['discountAmount'],
            'deliveryPrice' => $deliveryPrice,
            'totalPrice' => $totalPrice,
        ];
    }


    public function calculateServicePriceData(Menu $menu, int $numberOfPeople): array
    {
        $pricePerPerson = $menu->getPricePerPerson();

        $servicePriceBeforeDiscount = bcmul((string) $pricePerPerson, (string) $numberOfPeople, 2);

        $servicePrice = $servicePriceBeforeDiscount;

        $hasDiscount = false;
        $discountAmount = '0';

        // Apply a DISCOUNT_MULTIPLIER% (today = 10%) discount on the service price if the number of people is DISCOUNT_EXTRA_PEOPLE_THRESHOLD (today = 5) or more above the menu minimum 
        if ($numberOfPeople >= $menu->getMinimumNumberOfPeople() + AppConstant::DISCOUNT_EXTRA_PEOPLE_THRESHOLD) 
        {
            $servicePrice = bcmul($servicePrice, AppConstant::DISCOUNT_MULTIPLIER, 2);
            $hasDiscount = true;
            $discountAmount = bcsub($servicePriceBeforeDiscount, $servicePrice, 2);
        }

        return [
            'servicePriceBeforeDiscount' => $servicePriceBeforeDiscount,
            'servicePrice' => $servicePrice,
            'hasDiscount' => $hasDiscount,
            'discountAmount' => $discountAmount
        ];
    }

    public function calculateDeliveryPrice(float $distanceKm): string
    {
        $base = AppConstant::DELIVERY_BASE_PRICE;
        $pricePerKm = AppConstant::DELIVERY_PRICE_PER_KM;
        $distance = (string) $distanceKm;

        return bcadd($base, bcmul($pricePerKm, $distance, 2), 2);
    }


    public function calculateTotalPrice(string $servicePrice, string $deliveryPrice): string
    {
        return bcadd($servicePrice, $deliveryPrice, 2);
    }

}