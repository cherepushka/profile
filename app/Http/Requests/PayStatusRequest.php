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
         * data_shipment |array
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
            'method' => 'required|string',

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
