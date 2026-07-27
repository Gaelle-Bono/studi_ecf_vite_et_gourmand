<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\OpeningHoursException;

class JOpeningHoursExceptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $exceptions = [
            [
                'date' => '2026-10-31',
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '15:00:00',
                'eveningStart' => '17:00:00',
                'eveningEnd' => '23:00:00',
                'reason' => null,
            ],
            [
                'date' => '2026-12-25',
                'isClosed' => true,
                'morningStart' => null,
                'morningEnd' => null,
                'eveningStart' => null,
                'eveningEnd' => null,
                'reason' => null,
            ],
            [
                'date' => '2026-08-30',
                'isClosed' => false,
                'morningStart' => '10:00:00',
                'morningEnd' => '18:00:00',
                'eveningStart' => null,
                'eveningEnd' => null,
                'reason' => null,
            ],
        ];

        foreach ($exceptions as $data) {
            $exception = new OpeningHoursException();

            $exception->setDate(new \DateTimeImmutable($data['date']));
            $exception->setIsClosed($data['isClosed']);

            $exception->setMorningStart($this->toTime($data['morningStart']));
            $exception->setMorningEnd($this->toTime($data['morningEnd']));
            $exception->setEveningStart($this->toTime($data['eveningStart']));
            $exception->setEveningEnd($this->toTime($data['eveningEnd']));

            $exception->setReason($data['reason']);

            $manager->persist($exception);
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