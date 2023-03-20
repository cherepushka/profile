<?php

namespace App\Packages\Sms\Smsc;

use GuzzleHttp\Client;
use \Psr\Http\Client\ClientInterface;
use App\Packages\Sms\Smsc\Endpoint\Send;

// Документация - https://smsc.ru/api/#menu
final class Smsc{

    private string $base_endpoint = 'https://smsc.ru/sys/';

    public function __construct(
        private readonly string $login,
        private readonly string $password,
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
        return new Send($this->base_endpoint, $this->httpClient, $this->login, $this->password);
    }

}