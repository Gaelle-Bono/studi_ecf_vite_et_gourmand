<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Dish;
use App\Entity\Allergen;


class EDishFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dishes = [
            [
                'title' => 'Fondue au chocolat',
                'photoPath' => null,
                'allergens' => ['allergen-lait', 'allergen-oeufs'],
            ],
            [
                'title' => 'Agneau pascal',
                'photoPath' => 'agneau pascal.jpg',
                'allergens' => [],
            ],
            [
                'title' => 'Chapon farci',
                'photoPath' => 'chapon farci.jpg',
                'allergens' => [],
            ],
            [
                'title' => 'Îles flottantes',
                'photoPath' => 'iles flottantes.jpg',
                'allergens' => ['allergen-oeufs', 'allergen-lait'],
            ],
            [
                'title' => '6 huîtres',
                'photoPath' => 'huitres.jpg',
                'allergens' => ['allergen-mollusques'],
            ],
            [
                'title' => 'Foie gras',
                'photoPath' => 'foie gras.jpg',
                'allergens' => [],
            ],
        ];

        foreach ($dishes as $data) {
            $dish = new Dish();

            $dish->setTitle($data['title']);
            $dish->setPhotoPath($data['photoPath']);

            foreach ($data['allergens'] as $ref) {
                $dish->addAllergen($this->getReference($ref, Allergen::class));
            }

            $manager->persist($dish);

            // 🔑 Références pour MenuFixtures
            $this->addReference('dish-' . $this->slugify($data['title']), $dish);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DAllergenFixtures::class,
        ];
    }

    private function slugify(string $string): string
    {
        $string = mb_strtolower($string);

        $string = str_replace(
            [' ', 'à', 'â', 'ä', 'ç', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ÿ'],
            ['-', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'y'],
            $string
        );

        return $string;
    }
}
