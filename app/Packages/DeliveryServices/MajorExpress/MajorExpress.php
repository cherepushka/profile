<?php

namespace App\Packages\DeliveryServices\MajorExpress;

use App\Packages\DeliveryServices\MajorExpress\Endpoint\Queue;
use GuzzleHttp\Client;

class MajorExpress
{
    private string $base_endpoint = 'https://manager.fluid-line.ru/api/majorexpress/';
    private Client $httpClient;

    public function __construct(private readonly string $apiKey)
    {
        $this->httpClient = $this->getDefaultHttpClient();
    }

    private function getDefaultHttpClient(): Client
    {
        return new Client([
            'allow_redirects' => true,
            'base_url' => $this->base_endpoint,
        ]);
    }

    public function orders(): Queue
    {
        return new Queue(
            $this->base_endpoint,
            $this->httpClient,
            $this->apiKey,
        );
    }
}
