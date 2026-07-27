<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Allergen;

class DAllergenFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $allergens = [
            'gluten',
            'lait',
            'oeufs',
            'arachides',
            'fruits-a-coque',
            'soja',
            'poisson',
            'crustaces',
            'moutarde',
            'sesame',
            'celeri',
            'sulfites',
            'lupin',
            'mollusques',
        ];

        foreach ($allergens as $name) {
            $allergen = new Allergen();
            $allergen->setName($this->formatName($name));


            $this->addReference('allergen-' . $name, $allergen);

            
            $manager->persist($allergen);
        }

        $manager->flush();
    }

    private function formatName(string $name): string
    {
        return ucfirst(str_replace('-', ' ', $name));
    }
}
