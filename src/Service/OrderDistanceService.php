<?php

namespace App\Service;

class OrderDistanceService
{
    public function __construct(
        private CompanyService $companyService,
        private DistanceService $distanceService
    ) {}


    public function calculateDistanceFromAddresses(array $serviceAddress): float
    {
        $company = $this->companyService->getMainCompany();
        $fullCompanyAddress = $company?->getFullCompanyAddress();

        $fullServiceAddress = trim(
            $serviceAddress['street'] . ' ' . $serviceAddress['zip'] . ' ' . $serviceAddress['city']
        );

        return $this->distanceService->getDistanceBetweenAddresses(
            $fullCompanyAddress, 
            $fullServiceAddress
        );
    }

    public function calculateDistanceFromCoordinates(float $companyLat, float $companyLng, float $deliveryLat, float $deliveryLng): float 
    {
        return $this->distanceService->getDistanceBetweenCoordinates($companyLat, $companyLng, $deliveryLat, $deliveryLng);
    }


}