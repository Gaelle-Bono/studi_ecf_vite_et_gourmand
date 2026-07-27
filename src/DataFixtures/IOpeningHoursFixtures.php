<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\OpeningHours;

class IOpeningHoursFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $hours = [
            // Lundi (fermé)
            [
                'dayOfWeek' => 1,
                'isClosed' => true,
                'morningStart' => null,
                'morningEnd' => null,
                'eveningStart' => null,
                'eveningEnd' => null,
            ],

            // Mardi
            [
                'dayOfWeek' => 2,
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '14:00:00',
                'eveningStart' => '18:00:00',
                'eveningEnd' => '22:00:00',
            ],

            // Mercredi
            [
                'dayOfWeek' => 3,
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '14:00:00',
                'eveningStart' => '18:00:00',
                'eveningEnd' => '22:00:00',
            ],

            // Jeudi
            [
                'dayOfWeek' => 4,
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '14:00:00',
                'eveningStart' => '18:00:00',
                'eveningEnd' => '22:00:00',
            ],

            // Vendredi
            [
                'dayOfWeek' => 5,
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '14:00:00',
                'eveningStart' => '18:00:00',
                'eveningEnd' => '22:00:00',
            ],

            // Samedi
            [
                'dayOfWeek' => 6,
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '14:00:00',
                'eveningStart' => '18:00:00',
                'eveningEnd' => '22:00:00',
            ],

            // Dimanche (fermé)
            [
                'dayOfWeek' => 7,
                'isClosed' => true,
                'morningStart' => null,
                'morningEnd' => null,
                'eveningStart' => null,
                'eveningEnd' => null,
            ],
        ];

        foreach ($hours as $data) {
            $opening = new OpeningHours();

            $opening->setDayOfWeek($data['dayOfWeek']);
            $opening->setIsClosed($data['isClosed']);

            $opening->setMorningStart($this->toTime($data['morningStart']));
            $opening->setMorningEnd($this->toTime($data['morningEnd']));
            $opening->setEveningStart($this->toTime($data['eveningStart']));
            $opening->setEveningEnd($this->toTime($data['eveningEnd']));

            $manager->persist($opening);
        }

        $manager->flush();
    }

    private function toTime(?string $time): ?\DateTimeInterface
    {
        if ($time === null) {
            return null;
        }

        return new \DateTime('1970-01-01 ' . $time);
    }

}