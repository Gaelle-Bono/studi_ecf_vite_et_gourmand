<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Menu;
use App\Entity\OrderStatusHistory;

use App\Enum\OrderStatus;



class OrderFinalizationService
{

    public function __construct(
        private OrderBuilderService $orderBuilderService
    )
    {}

    public function finalizeOrder(Order $order, Menu $menu, \DateTimeInterface $serviceDate, string $requestedTime, array $summary, \DateTimeImmutable $createdAt): void
    {

        if (!$summary) {
            throw new \RuntimeException('Les données de commande sont introuvables');
        }

        $order->setOrderStatus(OrderStatus::PENDING);



        $requestedServiceDate = $this->orderBuilderService->buildRequestedDate($serviceDate, $requestedTime);
        $order->setRequestedDeliveryAt($requestedServiceDate);

        $this->setCoordinatesToOrder($order, $summary['coordinates']);
        $order->setDeliveryDistanceAtOrder($summary['distance']);

        $this->setPricesToOrder($order, $summary['pricing']);

        $this->setMenuDataToOrder($order, $menu);

        $order->setRequiresEquipmentLoanAtOrder($menu->requiresEquipmentLoan());
        $order->setIncludedEquipmentDescriptionAtOrder($menu->getIncludedEquipmentDescription());

        $currentStock = $menu->getRemainingQuantity();

        if ($currentStock < $order->getNumberOfPeople()) {
            throw new \RuntimeException(
                'La quantité disponible pour ce menu est insuffisante'
            );
        }

        $menu->setRemainingQuantity(bcsub($currentStock, $order->getNumberOfPeople()));

        $order->setCreatedAt($createdAt);

        $order->generateOrderNumber($createdAt);

    }


    private function setCoordinatesToOrder(Order $order, array $coords): void
    {
        $companyCoords = $coords['company'];
        $clientCoords = $coords['client'];

        $order
            ->setCompanyLatAtOrder($companyCoords['lat'])
            ->setCompanyLngAtOrder($companyCoords['lng'])
            ->setDeliveryLatAtOrder($clientCoords['lat'])
            ->setDeliveryLngAtOrder($clientCoords['lng']);
    }

    private function setPricesToOrder(Order $order, array $pricing): void
    {
        $order
            ->setServicePriceBeforeDiscountAtOrder($pricing['servicePriceBeforeDiscount'])
            ->setServicePriceAtOrder($pricing['servicePrice'])
            ->setDeliveryPriceAtOrder($pricing['deliveryPrice'])
            ->setTotalPriceAtOrder($pricing['totalPrice']);
    }

    private function setMenuDataToOrder(Order $order, Menu $menu): void
    {
        $order
            ->setMenuTitleAtOrder($menu->getTitle())
            ->setMenuDescriptionAtOrder($menu->getDescription())
            
            ->setStarterTitleAtOrder($menu->getStarter()?->getTitle())
            ->setStarterAllergensAtOrder($menu->getStarter()?->getAllergensAsString()?: null)


            ->setMainCourseTitleAtOrder($menu->getMainCourse()->getTitle())
            ->setMainCourseAllergensAtOrder($menu->getMainCourse()->getAllergensAsString()?: null)
            
            ->setDessertTitleAtOrder($menu->getDessert()?->getTitle())
            ->setDessertAllergensAtOrder($menu->getDessert()?->getAllergensAsString()?: null)

            ->setPricePerPersonAtOrder($menu->getPricePerPerson());
    }

    public function createInitialStatusHistory(Order $order, \DateTimeImmutable $createdAt): OrderStatusHistory
    {
        return new OrderStatusHistory($order->getUser(), $order, OrderStatus::PENDING, $createdAt);
    }

}