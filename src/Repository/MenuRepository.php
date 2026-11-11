<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

   public function findAvailableMenus(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isActive = true')
            ->andWhere('m.remainingQuantity > 0')
            ->getQuery()
            ->getResult();
    }
    

   public function findWithFilters(?int $dietId, ?int $themeId, ?float $minPricePerPerson, ?float $maxPricePerPerson, ?int $minimumNumberOfPeople): array
    {

        $qb = $this->createQueryBuilder('m');
        $qb->andWhere('m.isActive = true');
        $qb->andWhere('m.remainingQuantity > 0');


        if ($dietId !== null) {
            $qb->andWhere('m.diet = :dietId')
               ->setParameter('dietId', $dietId);
        }

        if ($themeId !== null) {
            $qb->andWhere('m.theme = :themeId')
               ->setParameter('themeId', $themeId);
        }

        if ($minPricePerPerson !== null) {
            $qb->andWhere('m.pricePerPerson >= :minPricePerPerson')
               ->setParameter('minPricePerPerson', $minPricePerPerson);
        }

        if ($maxPricePerPerson !== null) {
            $qb->andWhere('m.pricePerPerson <= :maxPricePerPerson')
               ->setParameter('maxPricePerPerson', $maxPricePerPerson);
        }

        if ($minimumNumberOfPeople !== null) {
            $qb->andWhere('m.minimumNumberOfPeople >= :minimumNumberOfPeople')
               ->setParameter('minimumNumberOfPeople', $minimumNumberOfPeople);
        }

        return $qb->getQuery()->getResult();
    }
}