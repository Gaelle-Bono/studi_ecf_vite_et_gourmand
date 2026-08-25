<?php

namespace App\Repository;

use App\Entity\Review;

use App\Enum\ReviewStatus;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findApprovedReviews(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.reviewStatus = :status')
            ->setParameter('status', ReviewStatus::APPROVED)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
