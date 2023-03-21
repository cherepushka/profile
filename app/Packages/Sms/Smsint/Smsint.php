<?php

namespace App\Packages\Sms\Smsint;

use GuzzleHttp\Client;
use \Psr\Http\Client\ClientInterface;
use App\Packages\Sms\Smsint\Endpoint\Send;

// Документация - https://lcab.smsint.ru/cabinet/json-doc/sender
final class Smsint{

    private string $base_endpoint = 'https://lcab.smsint.ru/json/v1.0/';

    public function __construct(
        private readonly string $token,
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

    public function send(): Send
    {
        return new Send($this->base_endpoint, $this->httpClient, $this->token);
    }

}