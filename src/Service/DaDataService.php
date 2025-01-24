<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DaDataService
{
    private $httpClient;
    private $apiKey;
    private $baseUrl;

    public function __construct(HttpClientInterface $httpClient, string $dadataApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $dadataApiKey;
        $this->baseUrl = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs';
    }

    public function suggestAddress(string $query, int $count = 5): array
    {
        $url = $this->baseUrl . '/suggest/address';
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $this->apiKey,
            ],
            'json' => [
                'query' => $query,
                'count' => $count,
            ],
        ]);

        return $response->toArray();
    }
}