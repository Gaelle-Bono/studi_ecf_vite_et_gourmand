<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;


class DistanceService
{
    public function __construct(
        private HttpClientInterface $httpClient, 
        private GeocodingService $geocodingService,
        private string $openRouteApiKey
        ) 
    {
    }

    public function getDistanceBetweenAddresses(string $companyAddress,string $clientAddress): float 
    {
        // Company localisation
        $companyCoords = $this->geocodingService->getCoordinates($companyAddress);

        // Client localisation
        $clientCoords = $this->geocodingService->getCoordinates($clientAddress);

        // Calculate distance via API de routing
        return $this->calculateRouteDistance(
            $companyCoords['lat'],
            $companyCoords['lng'],
            $clientCoords['lat'],
            $clientCoords['lng']
        );
    }

    private function calculateRouteDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): float 
    {
        $apiKey = $this->openRouteApiKey;

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

        $statusCode = $response->getStatusCode();
        $data = json_decode($response->getContent(false), true);

        // HTTP error
        if ($statusCode !== 200) {
            $this->throwFriendlyError($data);
        }

        // API error payload
        if (isset($data['error']['code'])) {
            $this->throwFriendlyError($data);
        }

        if (!isset($data['routes'][0]['summary']['distance'])) {
            throw new \RuntimeException('DISTANCE_ERROR: réponse API invalide');
        }

        return round($data['routes'][0]['summary']['distance'] / 1000, 2);
    }

    public function getDistanceBetweenCoordinates(float $fromLat, float $fromLng, float $toLat, float $toLng): float 
    {
        return $this->calculateRouteDistance($fromLat, $fromLng, $toLat, $toLng);
    }


    private function throwFriendlyError(array $data): void
    {
        $code = $data['error']['code'] ?? null;

        match ($code) {
            2010 => throw new \RuntimeException('DISTANCE_ERROR: clé API invalide'),
            2004 => throw new \RuntimeException('DISTANCE_ERROR: quota API dépassé'),
            2002 => throw new \RuntimeException('DISTANCE_ERROR: requête invalide (adresses)'),
            3001 => throw new \RuntimeException('DISTANCE_ERROR: coordonnées invalides'),
            default => throw new \RuntimeException('DISTANCE_ERROR: API routing indisponible'),
        };
    }

}