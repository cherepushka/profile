<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            /**
             * Boolean
             */
            'super_is_paid' => 'required',

            /**
             * Array: [id: [order_id, InvoiceId, contract_date, order_amount, paid_percent, paid_date, paid_amount, paid_detail] ]
             */
            'data' => 'required',

            /**
             * Array: [id: [ order_id, selling_currency, selling_detail, selling_amount, shipment_file] ]
             */
            'data_shipment' => 'required',
        ];
    }
}
