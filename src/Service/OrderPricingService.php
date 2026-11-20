<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\Order;
use App\Constant\AppConstant;


class OrderPricingService
{

    public function calculatePricesForOrder(Menu $menu, int $numberOfPeople, float $distance, ?Order $order = null): array
    {
        if ($order) {
            $pricePerPerson = $order->getPricePerPersonAtOrder();
            $minimumNumberOfPeople = $order->getMinimumNumberOfPeopleAtOrder();
        } else {
            $pricePerPerson = $menu->getPricePerPerson();
            $minimumNumberOfPeople = $menu->getMinimumNumberOfPeople();
        }

        $servicePriceData = $this->calculateServicePriceData(
            $pricePerPerson,
            $minimumNumberOfPeople,
            $numberOfPeople
        );

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


    public function calculateServicePriceData(string $pricePerPerson, int $minimumNumberOfPeople, int $numberOfPeople): array
    {
        $servicePriceBeforeDiscount = bcmul($pricePerPerson, (string) $numberOfPeople, 2);

        $servicePrice = $servicePriceBeforeDiscount;

        $hasDiscount = false;
        $discountAmount = '0';

        if ($numberOfPeople >= $minimumNumberOfPeople + AppConstant::DISCOUNT_EXTRA_PEOPLE_THRESHOLD) {
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