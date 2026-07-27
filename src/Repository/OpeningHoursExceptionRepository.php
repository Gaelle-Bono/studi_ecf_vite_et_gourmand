<?php

namespace App\Repository;

use App\Entity\OpeningHoursException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

/**
 * @extends ServiceEntityRepository<OpeningHoursException>
 */
class OpeningHoursExceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHoursException::class);
    }

    public function findOneByDate(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date = :date')
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
