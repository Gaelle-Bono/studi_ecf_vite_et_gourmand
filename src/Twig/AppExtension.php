<?php

namespace App\Twig;

use App\Service\OpeningHoursService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private OpeningHoursService $openingHoursService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'opening_hours',
                [$this->openingHoursService, 'getWeeklyOpeningHours']
            ),
        ];
    }
}