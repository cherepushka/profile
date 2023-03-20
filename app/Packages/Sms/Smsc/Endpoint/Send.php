<?php

namespace App\Packages\Sms\Smsc\Endpoint;

use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use \Psr\Http\Client\ClientInterface;
use RuntimeException;

// Документация - https://smsc.ru/api/#menu
final class Send{

    private array $defaultQueryParams;

    public function __construct(
        private readonly string $base_endpoint,
        private readonly ClientInterface $httpClient,
        private readonly string $login,
        private readonly string $password,
    ){
        $this->defaultQueryParams = [
            'sender' => 'Fluid-line',
            'login' => $login,
            'psw' => $password,
            'charset' => 'utf-8',
            'fmt' => 3,
            'maxsms' => 1,
            'cost' => 1
        ];
    }

    /**
     * Отправка СМС сообщения по указанным телефонным номерам
     * 
     * @param $message string - сообщение
     * @param $phones non-empty-array<string> - телефоны
     */
    public function sendSmsMessage(string $message, array $phones): void
    {
        if (empty($phones)){
            throw new InvalidArgumentException('$phones must not be empty');
        }

        $phonesQueryParam = implode(',', array_map(
            fn(string $phone) => '+' . preg_replace('#\D#', '', $phone), 
            $phones
        ));

        $endpoint = $this->base_endpoint . 'send.php?';
        $endpoint .= http_build_query([
            ...$this->defaultQueryParams,
            'mes' => $message,
            'phones' => $phonesQueryParam,
        ]);

        $request = new Request('GET', $endpoint);

        $response = $this->httpClient->sendRequest($request);
        $responseBody = json_decode($response->getBody()->getContents());

        if(property_exists($responseBody, 'error')){
            throw new RuntimeException($responseBody->error);
        }
    }

}