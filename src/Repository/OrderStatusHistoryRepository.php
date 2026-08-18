<?php

namespace App\Repository;

 
use App\Entity\OrderStatusHistory;
use App\Entity\Order;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderStatusHistory>
 */
class OrderStatusHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatusHistory::class);
    }

    public function findByOrder(Order $order): array
    {
        return $this->findBy(
            ['order' => $order],
            ['changedAt' => 'ASC']
        );
    }

}
