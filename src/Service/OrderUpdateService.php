<?php

namespace App\Service;

use App\Entity\Order;


class OrderUpdateService
{

    public function __construct(
        private OrderBuilderService $orderBuilderService
    )
    {}

    public function updateOrder(Order $order, array $summary, \DateTimeInterface $serviceDate, string $requestedTime, int $currentNumberOfPeople): void
    {

        //Service Date
        $order->setRequestedDeliveryAt(
            $this->orderBuilderService->buildRequestedDate(
                $serviceDate,
                $requestedTime
            )
        );

        //Service address
        $order
            ->setServiceAddress($summary['serviceAddress']['street'])
            ->setServiceAddressComplement($summary['serviceAddress']['complement'])
            ->setServiceZipCode($summary['serviceAddress']['zip'])
            ->setServiceCity($summary['serviceAddress']['city']);

        $order->setDeliveryInstructionsAtOrder(
            $summary['deliveryInstructions']
        );

        //Customer information
        $order
            ->setCustomerFirstNameAtOrder($summary['customer']['firstName'])
            ->setCustomerLastNameAtOrder($summary['customer']['lastName'])
            ->setCustomerEmailAtOrder($summary['customer']['email'])
            ->setCustomerPhoneAtOrder($summary['customer']['phone']);

        //Coordinates and distance
        $order
            ->setDeliveryDistanceAtOrder($summary['distance'])
            ->setDeliveryLatAtOrder($summary['coordinates']['client']['lat'])
            ->setDeliveryLngAtOrder($summary['coordinates']['client']['lng'])
            ->setCompanyLatAtOrder($summary['coordinates']['company']['lat'])
            ->setCompanyLngAtOrder($summary['coordinates']['company']['lng']);

        //Prices
        $pricing = $summary['pricing'];
        $order
            ->setServicePriceBeforeDiscountAtOrder($pricing['servicePriceBeforeDiscount'])
            ->setServicePriceAtOrder($pricing['servicePrice'])
            ->setDeliveryPriceAtOrder($pricing['deliveryPrice'])
            ->setTotalPriceAtOrder($pricing['totalPrice']);

        //Update Order UpdatedAt
        $order->setUpdatedAt(new \DateTimeImmutable());

    }

}
