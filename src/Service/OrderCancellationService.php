<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderStatusHistory;
use App\Entity\OrderCancellation;
use App\Repository\OrderStatusRepository;

class OrderCancellationService
{
    public function __construct(
        private OrderStatusRepository $orderStatusRepository
    ) {}

    public function cancel(Order $order): array
    {
        // Update Order Status to Cancelled
        $cancelledStatus = $this->orderStatusRepository->findOneBy(['code' => 'CANCELLED']);
        $order->setOrderStatus($cancelledStatus);
        
        //Update Menu Remaining Quantity
        $menu = $order->getMenu();
        $menu->setRemainingQuantity(
            $menu->getRemainingQuantity() + $order->getNumberOfPeople()
        );
            
        //Update Order UpdatedAt
        $order->setUpdatedAt(new \DateTimeImmutable());

        //create a line in OrderStatusHistory
        $statusHistory = new OrderStatusHistory($order->getUser(), $order, $cancelledStatus);
        
        //create a line in OrderCancellation
        $cancellation = new OrderCancellation($order, $order->getUser());

        return [
            'statusHistory' => $statusHistory, 
            'cancellation' => $cancellation
        ];
    }
}
