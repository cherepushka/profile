<?php

namespace App\Http\Requests;

use App\Http\Actions\OrderList\Rules\Filterable;
use App\Http\Actions\OrderList\Rules\Sortable;
use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'order' => ['nullable', 'alpha_dash:ascii', new Sortable()],
            'sort' => ['nullable', 'array'],
            'sort.*' => ['string', new Filterable()]
        ];
    }
}
