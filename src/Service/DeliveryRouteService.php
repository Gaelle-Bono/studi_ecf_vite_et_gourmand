<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Repository\CompanyRepository;


class DeliveryRouteService
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private GeocodingService $geocodingService,
        private HttpClientInterface $httpClient, 
        private string $openRouteApiKey
    ) {}

    public function getRouteData(array $serviceAddress): array 
    {

        $company = $this->companyRepository->getCompany();

        $clientCoords = $this->getClientCoordinates($serviceAddress);

        $distance = $this->calculateRouteDistance(
            $company->getLatitude(),
            $company->getLongitude(),
            $clientCoords['lat'],
            $clientCoords['lng']
        );

        return [
            'coordinates' => [
                'company' => [
                    'lat' => $company->getLatitude(),
                    'lng' => $company->getLongitude(),
                ],
                'client' => $clientCoords,
            ],
            'distance' => $distance
        ];
    }


    private function getClientCoordinates(array $serviceAddress): array
    {
        $fullAddress = trim(
            $serviceAddress['street'] . ' ' .
            $serviceAddress['zip'] . ' ' .
            $serviceAddress['city']
        );

        return $this->geocodingService->getCoordinates($fullAddress);
    }


    private function calculateRouteDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): float 
    {
        $apiKey = $this->openRouteApiKey;

        dump(strlen($apiKey));


        $response = $this->httpClient->request('POST',
            'https://api.openrouteservice.org/v2/directions/driving-car',
            [
                'headers' => [
                    'Authorization' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'coordinates' => [
                        [$fromLng, $fromLat],
                        [$toLng, $toLat],
                    ],
                ],
            ]
        );

        $data = json_decode($response->getContent(false), true);

        $distance = $data['routes'][0]['summary']['distance'] ?? null;

        if ($distance === null) {
            throw new \RuntimeException('Le service de calcul de distance est momentanément indisponible');
        }

        return round($distance / 1000, 2);

    }

}