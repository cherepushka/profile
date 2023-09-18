<?php

namespace App\Packages\Payments\Cloudpayments\Endpoint;

use GuzzleHttp\Psr7\Request;
use App\Packages\Payments\Cloudpayments\Dto\Payment;
use App\Packages\Payments\Cloudpayments\RequestMappers\OrderCreate;
use Psr\Http\Client\ClientInterface;
use RuntimeException;

final class Orders
{
    private array $defaultHeaders;

    public function __construct(
        private readonly string $base_endpoint,
        private readonly ClientInterface $httpClient,
        private readonly string $id,
        private readonly string $key,
    ) {
        $this->defaultHeaders = [
            'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $id, $key)),
        ];
    }

    /**
     * Формирование ссылки на оплату
     */
    public function create(Payment $payment): string
    {
        $endpoint = $this->base_endpoint . 'orders/create';

        $headers = [
            ...$this->defaultHeaders,
            'Content-Type' => 'application/json'
        ];

        $requestBody = (new OrderCreate())->getRequestBody($payment);

        $request = new Request('POST', $endpoint, $headers, $requestBody);

        $response = $this->httpClient->sendRequest($request);
        $responseBody = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() !== 200) {

            $message = property_exists($responseBody, 'Message')
                ? $responseBody->Message
                : 'Не удалось создать ссылку на заказ';

            throw new RuntimeException($message);
        }

        return $responseBody->Model->Url;
    }

}
