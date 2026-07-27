<?php

namespace App\Service;

use App\Entity\Menu;
use App\Constant\AppConstant;


class OrderValidationService
{

    public function __construct(
        private OpeningHoursService $openingHoursService
    ) {}

    public function validate(Menu $menu, int $numberOfPeople, \DateTimeInterface $serviceDate, string $requestedTime): array
    {
        $errors = [];

        //Date validation : service date must be at least X days after order date, X being defined in menu details
        $today = new \DateTimeImmutable('today');
        $minimumDays = $menu->getMinimumDaysBeforeOrder();
        if ($minimumDays !== null) {
            $minimumDate = $today->modify("+{$minimumDays} days");
            if ($serviceDate < $minimumDate) {
                $errors[] = [
                    'field' => 'serviceDate',
                    'message' => "Ce menu doit être commandé au moins {$minimumDays} jours à l’avance"
                ];
            }
        }

        //number of people validation : cannot be less than menu minimum nor more than remaining quantity
        if ($numberOfPeople < $menu->getMinimumNumberOfPeople()) {
            $errors[] = [
                'field' => 'numberOfPeople',
                'message' => $menu->getMinimumNumberOfPeople() . ' personnes minimum pour ce menu'
            ];
        }
        
        // stock validation 
        if ($numberOfPeople > $menu->getRemainingQuantity()) {
            $errors[]= [
                'field' => 'numberOfPeople',
                'message' => 'Ce menu est encore disponible pour ' 
                    . $menu->getRemainingQuantity() . ' personne(s) maximum'
            ];
        }

        //closed days 
        $availabilityForDate = $this->openingHoursService->getAvailabilityForDate($serviceDate);
        if ($availabilityForDate['isClosed']) {
            $errors[] = [
                'field' => 'serviceDate',
                'message' => AppConstant::CLOSED
            ];
            return $errors; 
        }
        
        //hours validation (tested only if company is open)
        if (!$this->openingHoursService->isTimeAllowed($requestedTime, $availabilityForDate['ranges'])) {
            $errors[] = [
                'field' => 'requestedTime',
                'message' => "L'heure choisie ne correspond pas aux créneaux disponibles pour cette date"
            ];
        }
        return $errors;
    }

}