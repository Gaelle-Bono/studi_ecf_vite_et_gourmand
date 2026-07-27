<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Theme;

class BThemeFixtures extends Fixture
{
      public function load(ObjectManager $manager): void
    {
        $themes = [
            [
                'name' => 'Noël',
                'ref' => 'theme-noel',
            ],
            [
                'name' => 'Pâques',
                'ref' => 'theme-paques',
            ],
            [
                'name' => 'Classique',
                'ref' => 'theme-classique',
            ],
            [
                'name' => 'Evènement',
                'ref' => 'theme-evenement',
            ],
        ];

        foreach ($themes as $data) {
            $theme = new Theme();
            $theme->setName($data['name']);

            $this->addReference($data['ref'], $theme);

            $manager->persist($theme);
        }

        $manager->flush();
    }
}
