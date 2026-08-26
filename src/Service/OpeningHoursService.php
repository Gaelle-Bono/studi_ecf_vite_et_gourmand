<?php

namespace App\Service;

use App\Repository\OpeningHoursRepository;
use App\Repository\OpeningHoursExceptionRepository;

use App\Entity\OpeningHours;
use App\Entity\OpeningHoursException;

use App\Constant\AppConstant;



class OpeningHoursService
{
    

    public function __construct(
        private OpeningHoursRepository $openingHoursRepository,
        private OpeningHoursExceptionRepository $exceptionRepository
    ){}


    public function getWeeklyOpeningHours(): array
    {
        $openingHours = $this->openingHoursRepository->findBy(
            [],
            ['dayOfWeek' => 'ASC']
        );

        $result = [];

        foreach ($openingHours as $openingHour) {
            $ranges = $this->buildRanges($openingHour);

            $result[] = [
                'day' => AppConstant::DAYS_OF_WEEK[$openingHour->getDayOfWeek()],
                'isClosed' => $openingHour->isClosed(),
                'hours' => $openingHour->isClosed()
                    ? null
                    : $this->formatToText($ranges),
            ];
        }

        return $result;
    }


    private function formatToText(array $ranges): string
    {
        $parts = [];

        foreach ($ranges as $range) {
            $parts[] = $range['start'] . ' - ' . $range['end'];
        }

        return implode(' / ', $parts);
    }

    

    public function isTimeAllowed(string $time, array $ranges): bool
    {
        foreach ($ranges as $range) {
            if ($time >= $range['start'] && $time <= $range['end']) {
                return true;
            }
        }

        return false;
    }

    public function getAvailabilityForDate(\DateTimeInterface $date): array
    {
        $openDay = $this->resolveOpenDay($date);
       
        if (!$openDay || $openDay->isClosed()) {
            return [
                'isClosed' => true,
                'ranges' => []
            ];
        }

        return [
            'isClosed' => false,
            'ranges' => $this->buildRanges($openDay)
        ];

    }

    private function resolveOpenDay(\DateTimeInterface $date)
    {
        $exception = $this->exceptionRepository->findOneByDate($date);

        if ($exception) {
            return $exception;
        }

        $dayOfWeek = (int) $date->format('N');
        return $this->openingHoursRepository->findOneBy([
            'dayOfWeek' => $dayOfWeek
        ]);

    }


    private function buildRanges(OpeningHours|OpeningHoursException $openDay): array
    {
        $ranges = [];

        if ($openDay->getMorningStart() && $openDay->getMorningEnd()) {
            $ranges[] = [
                'start' => $openDay->getMorningStart()->format('H:i'),
                'end' => $openDay->getMorningEnd()->format('H:i'),
            ];
        }

        if ($openDay->getEveningStart() && $openDay->getEveningEnd()) {
            $ranges[] = [
                'start' => $openDay->getEveningStart()->format('H:i'),
                'end' => $openDay->getEveningEnd()->format('H:i'),
            ];
        }
        return $ranges;
    }

    public function getOpeningHoursForDate(\DateTimeInterface $date): array
    {
        $availability = $this->getAvailabilityForDate($date);

        if ($availability['isClosed']) {
            return [
                'isClosed' => true,
                'message' => AppConstant::CLOSED,
                'openingHoursText' => null
            ];
        }

        return [
            'isClosed' => false,
            'message' => null,
            'openingHoursText' => $this->formatToText($availability['ranges'])
        ];
    }

    
}
