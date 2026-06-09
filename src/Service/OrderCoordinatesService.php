<?php

namespace App\Service;

use App\Entity\Order;


class OrderCoordinatesService
{
    public function __construct(
        private CompanyService $companyService,
        private GeocodingService $geocodingService
    ) {}

    public function getCoordinatesForOrder(Order $order): array
    {
        $company = $this->companyService->getMainCompany();

        $companyCoords = $this->geocodingService->getCoordinates(
            $company?->getFullCompanyAddress()
        );

        $clientCoords = $this->geocodingService->getCoordinates(
            $order->getFullServiceAddress()
        );

        return [
            'company' => $companyCoords,
            'client' => $clientCoords,
        ];
    }
}