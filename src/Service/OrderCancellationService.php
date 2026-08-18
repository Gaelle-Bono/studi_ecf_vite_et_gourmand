<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderStatusHistory;
use App\Entity\OrderCancellation;

use App\Enum\OrderStatus;

class OrderCancellationService
{
    public function cancel(Order $order, \DateTimeImmutable $cancelledAt): void
    {
        $order->setOrderStatus(OrderStatus::CANCELLED);

        $menu = $order->getMenu();

        $menu->setRemainingQuantity(
            $menu->getRemainingQuantity() + $order->getNumberOfPeople()
        );

        $order->setUpdatedAt($cancelledAt);
    }

    public function createStatusHistory(Order $order, \DateTimeImmutable $cancelledAt): OrderStatusHistory
    {
        return new OrderStatusHistory($order->getUser(), $order, OrderStatus::CANCELLED, $cancelledAt);
    }


    public function createCancellation(Order $order): OrderCancellation
    {
        return new OrderCancellation($order, $order->getUser());
    }

}
