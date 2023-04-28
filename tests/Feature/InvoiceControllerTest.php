<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_error_if_invalid_one_field_type()
    {
        $response = $this->post('/api/collect/invoice', [
            'filename' => 'test.zip',
            'file' => 'dGVzdA==',
            'link' => '',
            'entity' => true,
            'email' => 'string',
            'email_hash' => 'string',
            'responsible' => 'string',
            'order_id' => 'string',
            'InvoiceId' => 123,
            'InvoiceDate' => 'string',
            'pay_block' => 123, // ошибка должна быть тут
            'client_id' => 'string',
            'client_code' => 'string',
            'Invoice_currency' => 'string',
            'Invoice_price' => 832948.76,
            'Invoice_data' => [
                'array'
            ],
            'filepswd' => 'string',
            'roistat_id' => 'string',
            'deal_source' => 'string',
        ]);

        $response->assertStatus(422);
    }
}
