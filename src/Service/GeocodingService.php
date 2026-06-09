<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    public function __construct(private HttpClientInterface $httpClient) {
    }

    public function getCoordinates(string $address): array
    {
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'vite-et-gourmand'
            ]
        ]);

        $data = $response->toArray();

        if (empty($data)) {
            throw new \RuntimeException('GEOCODING_ERROR: Adresse introuvable');
        }

        return [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon'],
        ];
    }
}