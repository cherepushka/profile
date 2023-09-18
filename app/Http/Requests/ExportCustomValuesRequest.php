<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportCustomValuesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->header('Token') === config('app.export_token');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from_timestamp' => 'numeric',
            'to_timestamp' => 'numeric',
            'offset' => 'numeric',
            'limit' => 'numeric'
        ];
    }
}
