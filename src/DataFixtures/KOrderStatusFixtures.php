<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\OrderStatus;

class KOrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            'PENDING' => 'En attente',
            'CONFIRMED' => 'Confirmée',
            'PREPARING' => 'En préparation',
            'READY' => 'Prête',
            'DELIVERING' => 'En livraison',
            'COMPLETED' => 'Terminée',
            'CANCELLED' => 'Annulée',
        ];

        foreach ($statuses as $code => $label) {
            $status = new OrderStatus();
            $status->setCode($code);
            $status->setLabel($label);

            $manager->persist($status);
        }

        $manager->flush();
    }
}
