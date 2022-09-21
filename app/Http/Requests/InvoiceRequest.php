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
            'link' => 'required|string',
            'entity' => 'required|boolean', // Json return [ true / false ]
            'email' => 'required|string', // Email owner
            'email_hash' => 'required|string', // Email hash
            'responsible' => 'required|string', // Email manager
            'order_id' => 'required|string',
            'InvoiceId' => 'required|integer',
            'InvoiceDate' => 'required|string',
            'pay_block' => 'required|boolean', // Json return [ true / false ]
            'client_id' => 'required|string',
            'client_code' => 'required|string',
            'Invoice_currency' => 'required|string',
            'Invoice_price' => 'required|numeric',
            'Invoice_data' => 'required|array',
//            'client_destination' => 'required|string', // Not used in debug mode
//            'phone_number' => 'required|string', // Not used in debug mode
            'filepswd' => 'required|string',
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
        Storage::disk('requests')->put('/logs/failed-invoice-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
        parent::failedValidation($validator);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        Storage::disk('requests')->put('/logs/before-valid-invoice-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
    }
}
