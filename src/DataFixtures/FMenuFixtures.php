<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Menu;

class FMenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // =========================
        // MENU DE NOËL
        // =========================
        $menu = new Menu();
        $menu->setTitle('Menu de Noël');
        $menu->setMinimumNumberOfPeople(4);
        $menu->setPricePerPerson(50.00);
        $menu->setDescription('Un joli menu de Noël');
        $menu->setRemainingQuantity(10);

        $menu->setConditions([
            "à commander 5 jours à l'avance",
            "huîtres à conserver au réfrigérateur avant de servir"
        ]);

        $menu->setDiet($this->getReference('diet-classique', \App\Entity\Diet::class));
        $menu->setTheme($this->getReference('theme-noel', \App\Entity\Theme::class));

        $menu->setStarter($this->getReference('dish-6-huitres', \App\Entity\Dish::class));
        $menu->setMainCourse($this->getReference('dish-chapon-farci', \App\Entity\Dish::class));
        $menu->setDessert($this->getReference('dish-iles-flottantes', \App\Entity\Dish::class));

        $menu->setMinimumDaysBeforeOrder(5);
        $menu->setRequiresEquipmentLoan(false);
        $menu->setIncludedEquipmentDescription(null);

        $manager->persist($menu);

        // =========================
        // MENU DE PÂQUES
        // =========================
        $menu = new Menu();
        $menu->setTitle('Menu de Pâques');
        $menu->setMinimumNumberOfPeople(4);
        $menu->setPricePerPerson(30.00);
        $menu->setDescription('Traditionnel menu de Pâques');
        $menu->setRemainingQuantity(10);

        $menu->setConditions(null);

        $menu->setDiet($this->getReference('diet-classique', \App\Entity\Diet::class));
        $menu->setTheme($this->getReference('theme-paques', \App\Entity\Theme::class));

        $menu->setStarter($this->getReference('dish-foie-gras', \App\Entity\Dish::class));
        $menu->setMainCourse($this->getReference('dish-agneau-pascal', \App\Entity\Dish::class));
        $menu->setDessert($this->getReference('dish-fondue-au-chocolat', \App\Entity\Dish::class));

        $menu->setMinimumDaysBeforeOrder(1);
        $menu->setRequiresEquipmentLoan(true);
        $menu->setIncludedEquipmentDescription('fontaine à chocolat, spatules');

        $manager->persist($menu);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CDietFixtures::class,
            BThemeFixtures::class,
            EDishFixtures::class,
        ];
    }
}
