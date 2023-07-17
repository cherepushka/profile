<?php

namespace App\Packages\DeliveryServices\MajorExpress\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

readonly class Queue
{
    public function __construct(
        private string $base_endpoint,
        private Client $httpClient,
        private string $apiKey,
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws RuntimeException
     */
    public function enqueue(string $deliveryId): void
    {
        $endpoint = $this->base_endpoint . 'queue/create';

        $request = $this->httpClient->post($endpoint, [
            RequestOptions::FORM_PARAMS => [
                'APP_SECRET' => $this->apiKey,
                'code' => $deliveryId
            ]
        ]);

        $response = json_decode($request->getBody()->getContents());

        if(property_exists($response, 'message')) {
            if($response->message !== 'Код уже существует.' && $response->message !== 'Код добавлен в очередь.') {
                throw new RuntimeException('Сервер ответил ошибкой: ' . $response);
            }
        }
    }

    /**
     * @throws GuzzleException
     */
    public function fetchStatus(string $deliveryId): array
    {
        $endpoint = $this->base_endpoint . 'invoice/everything';

        $request = $this->httpClient->post($endpoint, [
            RequestOptions::FORM_PARAMS => [
                'APP_SECRET' => $this->apiKey,
                'code' => $deliveryId,
                'type' => 'json'
            ]
        ]);

        return json_decode($request->getBody()->getContents(), true);
    }

}
