<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\Order;

use App\Constant\AppConstant;


class OrderValidationService
{

    public function __construct(
        private OpeningHoursService $openingHoursService
    ) {}

    public function validate(Menu $menu,int $numberOfPeople, \DateTimeInterface $serviceDate, string $requestedTime, int $currentNumberOfPeople = 0,  ?Order $order = null): array
    {
        $errors = [];

        // Date validation : service date must be at least tomorrow
        $tomorrow = new \DateTimeImmutable('tomorrow');
        if ($serviceDate < $tomorrow) {
            $errors[] = [
                'field' => 'serviceDate',
                'message' => 'La prestation doit être réservée au minimum pour demain'
            ];
        }
                
        //Date validation : service date must be at least X days after order date, X being defined in menu details
        $today = new \DateTimeImmutable('today');

        if ($order) {
            $minimumDays = $order->getMinimumDaysBeforeOrderAtOrder();
            $minimumPeople = $order->getMinimumNumberOfPeopleAtOrder();
        } else {
            $minimumDays = $menu->getMinimumDaysBeforeOrder();
            $minimumPeople = $menu->getMinimumNumberOfPeople();
        }

        $minimumDate = $today->modify("+{$minimumDays} days");
        if ($serviceDate < $minimumDate) {
            $errors[] = [
                'field' => 'serviceDate',
                'message' => "Ce menu doit être commandé au moins {$minimumDays} jours à l’avance"
            ];
        }

        //number of people validation : cannot be less than menu minimum nor more than remaining quantity
        
        if ($numberOfPeople < $minimumPeople) {
            $errors[] = [
                'field' => 'numberOfPeople',
                'message' => $minimumPeople . ' personnes minimum pour ce menu'
            ];
        }
        
              
        // Stock validation
        $availableQuantity = $menu->getRemainingQuantity() + $currentNumberOfPeople;

        if ($numberOfPeople > $availableQuantity) {

            if ($availableQuantity === 0) {
                $message = 'Ce menu est actuellement en rupture de stock. La commande est momentanément impossible.';
            } else {
                $message = 'Le stock disponible est insuffisant. Il permet actuellement de servir '
                    . $availableQuantity . ' personne(s) maximum.';
            }

            $errors[] = [
                'field' => 'numberOfPeople',
                'message' => $message
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

    public function validateNumberOfPeople(Menu $menu, int $numberOfPeople, int $currentNumberOfPeople = 0): ?string 
    {
        if ($numberOfPeople < $menu->getMinimumNumberOfPeople()) {
            return $menu->getMinimumNumberOfPeople() . ' personnes minimum pour ce menu';
        }

        $availableQuantity = $menu->getRemainingQuantity() + $currentNumberOfPeople;

        if ($numberOfPeople > $availableQuantity) {
            return 'Ce menu est encore disponible pour ' . $availableQuantity . ' personne(s) maximum';
        }

        return null;
    }

}