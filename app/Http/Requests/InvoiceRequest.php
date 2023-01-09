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
            'entity' => 'required|boolean',
            'email' => 'required|string', // Email owner
            'email_hash' => 'required|string', // Email hash
            'responsible' => 'required|string', // Email manager
            'order_id' => 'required|string',
            'InvoiceId' => 'required|integer',
            'InvoiceDate' => 'required|string',
            'pay_block' => 'required|boolean',
            'client_id' => 'required|string',
//            'contract_date' => 'required|datetime', // - Отсутствует
            'client_code' => 'required|string',
            'Invoice_currency' => 'required|string',
            'Invoice_price' => 'required|numeric',
//            'phone' => 'required|string', // - Отсутствует
//            'order_amount' => 'required|numeric' // - Отсутствует

            /**
             * Array: [id: [ vendor_code, product_id, product_name, product_category, product_unit, product_qty, product_price, product_sum, product_vat, sum_vat, product_sum_vat ] ]
             */
            'Invoice_data' => 'required|array',
//            'client_destination' => 'required|string', // - Отсутствует
//            'phone_number' => 'required|string', // - Отсутствует
            'filepswd' => 'required|string',
//            'roistat_id' => 'required|string', // - Отсутствует
//            'deal_source' => 'required|string', // - Отсутствует
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
