<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'filename' => 'required|string',
            'file' => 'required|string',
            'link' => '',
            'entity' => 'required|boolean',
            'email' => 'required|string', // Email owner
            'email_hash' => 'required|string', // Email hash
            'responsible' => 'required|string', // Email manager
            'order_id' => 'required|string',
            'InvoiceId' => 'required|integer',
            'InvoiceDate' => 'required|string',
            'pay_block' => 'required|boolean',
            'client_id' => 'required|string',
            'client_code' => 'required|string',
            'Invoice_currency' => 'required|string',
            'Invoice_price' => 'required|numeric',

            'Invoice_data' => 'required|array',
            'client_destination' => 'string|nullable',
            'phone_number' => 'string|nullable',
            'filepswd' => 'required|string',
            'roistat_id' => 'string|nullable',
            'deal_source' => 'string|nullable',
        ];
    }

    /**
     * Сохранение запроса json при его ошибке
     *
     * @param Validator $validator
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        Storage::disk('requests')->put('/before/failed/invoice-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
        parent::failedValidation($validator);
    }

    /**
     * Подготовка данных к валидации
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        Storage::disk('requests')->put('/before/get/invoice-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
    }
}
