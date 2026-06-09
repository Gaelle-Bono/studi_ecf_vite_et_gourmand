<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Menu;

use App\Repository\OrderStatusRepository;


class OrderFinalizationService
{

    public function __construct(
        private OrderBuilderService $orderBuilderService,
        private OrderStatusRepository $orderStatusRepository,
        private OrderCoordinatesService $orderCoordinatesService,
        private OrderDistanceService $orderDistanceService,
        private OrderPricingService $orderPricingService
    ) {}

    public function finalizeOrder(Order $order, Menu $menu, \DateTimeInterface $serviceDate, \DateTimeInterface $requestedTime): void
    {
        // status
        $order->setOrderStatus(
            $this->orderStatusRepository->findOneBy(['code' => 'PENDING'])
        );

        // date
        $order->setRequestedDeliveryAt(
            $this->orderBuilderService->buildRequestedDate($serviceDate, $requestedTime)
        );

        // coordinates and distance
        $distance = 0;

        try {
            $coords = $this->orderCoordinatesService->getCoordinatesForOrder($order);
            $this->setCoordinatesToOrder($order, $coords);

            $distance = $this->orderDistanceService->calculateDistanceFromCoordinates(
                $coords['company']['lat'],
                $coords['company']['lng'],
                $coords['client']['lat'],
                $coords['client']['lng']
            );
            $order->setDeliveryDistanceAtOrder($distance);

        } catch (\RuntimeException $e) {
            throw new \RuntimeException(
                'Impossible de calculer les frais de livraison. Veuillez réessayer plus tard.',
                0,
                $e
            );
        }

        // pricing ONLY if distance is valid
        $pricing = $this->orderPricingService->calculatePricesForOrder(
            $menu,
            $order->getNumberOfPeople(),
            $distance
        );

        $this->setPricesToOrder($order, $pricing);


        // equipment loan 
        $order->setRequiresEquipmentLoanAtOrder(
            $menu->requiresEquipmentLoan()
        );
        

        // order number
        $order->generateOrderNumber();
    }


    private function setCoordinatesToOrder(Order $order, array $coords): void
    {
        $companyCoords = $coords['company'];
        $clientCoords = $coords['client'];

        $order->setCompanyLatAtOrder($companyCoords['lat']);
        $order->setCompanyLngAtOrder($companyCoords['lng']);
        $order->setDeliveryLatAtOrder($clientCoords['lat']);
        $order->setDeliveryLngAtOrder($clientCoords['lng']);
    }


    private function setPricesToOrder(Order $order, array $pricing): void
    {
        $order
            ->setServicePriceBeforeDiscountAtOrder($pricing['servicePriceBeforeDiscount'])
            ->setServicePriceAtOrder($pricing['servicePrice'])
            ->setDeliveryPriceAtOrder($pricing['deliveryPrice'])
            ->setTotalPriceAtOrder($pricing['totalPrice']);
    }


}