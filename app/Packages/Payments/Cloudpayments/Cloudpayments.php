<?php

namespace App\Packages\Payments\Cloudpayments;

use App\Packages\Payments\Cloudpayments\Endpoint\Orders;
use GuzzleHttp\Client;
use \Psr\Http\Client\ClientInterface;

final class Cloudpayments{

    private string $base_endpoint = 'https://api.cloudpayments.ru/';

    public function __construct(
        private readonly string $id,
        private readonly string $key,
        private ?ClientInterface $httpClient = null,
    )
    {
        if ($this->httpClient === null){
            $this->httpClient = $this->getDefaultHttpClient();
        }
    }

    private function getDefaultHttpClient(): ClientInterface
    {
        return new Client([
            'allow_redirects' => true,
            'base_url' => $this->base_endpoint,
        ]);
    }

    public function orders(): Orders
    {
        return new Orders(
            $this->base_endpoint, 
            $this->httpClient, 
            $this->id, 
            $this->key
        );
    }

}