<?php

namespace App\DataFixtures;
use App\Entity\Diet;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CDietFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $diets = [
            'classique',
            'végétarien',
            'végan',
            'halal',
        ];

        foreach ($diets as $name) {
            $diet = new Diet();
            $diet->setName($name);

            $this->addReference('diet-' . $name, $diet);

            $manager->persist($diet);
        }

        $manager->flush();
    }
}
