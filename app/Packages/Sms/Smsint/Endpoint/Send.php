<?php

namespace App\Packages\Sms\Smsint\Endpoint;

use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use \Psr\Http\Client\ClientInterface;
use RuntimeException;

// Документация - https://lcab.smsint.ru/cabinet/json-doc/sender
final class Send{

    private array $defaultHeaders;

    public function __construct(
        private readonly string $base_endpoint,
        private readonly ClientInterface $httpClient,
        string $token,
    ){
        $this->defaultHeaders = [
            'X-Token' => $token,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Отправка СМС сообщения по указанным телефонным номерам
     * 
     * Документация - https://lcab.smsint.ru/cabinet/json-doc/sender#tag/Sms/paths/~1sms~1send~1text/post
     * 
     * @param $message string - сообщение
     * @param $phones non-empty-array<string> - телефоны
     */
    public function sendSmsMessage(string $message, array $phones): void
    {
        if (empty($phones)){
            throw new InvalidArgumentException('$phones must not be empty');
        }

        $phonesValidated = array_map(
            fn(string $phone) => preg_replace('#\D#', '', $phone), 
            $phones
        );

        $endpoint = $this->base_endpoint . 'sms/send/text';
        $requestBody = [
            'messages' => [],
            'validate' => false,
            'duplicateRecipientsAllowed' => false,
            'channel' => 1,
            'transliterate' => false,
        ];
        foreach($phonesValidated as $phone){
            $requestBody['messages'][] = [
                'recipient' => $phone,
                'recipientType' => 'recipient',
                'id' => uniqid(),
                'source' => 'fluid-line',
                'timeout' => 60,
                'shortenUrl' => false,
                'text' => $message,
            ];
        }

        $request = new Request('POST', $endpoint, $this->defaultHeaders, json_encode($requestBody));

        $response = $this->httpClient->sendRequest($request);
        $responseBody = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() !== 200 || !$responseBody->success){
            throw new RuntimeException($responseBody->error->descr);
        }
    }

}