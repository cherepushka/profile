<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class PayStatusRequest extends FormRequest
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
        /**
         * JSON Structure
         *
         * method
         *     payStatusUpdate |string
         *
         * super_is_paid
         *     true/false |boolean
         *
         * data |array
         *     0 => |index
         *         order_id |string
         *         InvoiceId |string (int)
         *         contract_date |string (datetime) (Y-m-d H:i:s)
         *         order_amount |string (double)
         *         paid_percent |string (int)
         *         paid_date |string (datetime) (Y-m-d H:i:s)
         *         paid_amount |string (double)
         *         paid_detail |array
         *             0 => |index
         *                 paid_date |string (datetime) (Y-m-d H:i:s)
         *                 paid_amount |string (double)
         *                 paid_percent |string (int)
         *
         * data_shipment null|array
         *     0 => |index
         *         order_id |string
         *         selling_currency |string
         *         selling_detail |array
         *         selling_amount |string (double)
         *         shipment_file |array
         *             file_name |string
         *             file_data |string (base64)
         *             file_pswd |string
         */
        return [
            'method' => '',
            'super_is_paid' => '',
            'data' => 'required',
            'data_shipment' => 'array',
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
        Storage::disk('requests')->put('/before/failed/payStatusUpdate-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
        parent::failedValidation($validator);
    }

    /**
     * Подготовка данных к валидации
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        Storage::disk('requests')->put('/before/get/payStatusUpdate-json-'.date("h-i-s-d-m-Y").'.json', json_encode($this->request->all()));
    }
}
