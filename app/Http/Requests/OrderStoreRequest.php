<?php

namespace App\Http\Requests;

use App\Rules\StockControlRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'customerId' => ['required', 'numeric', 'exists:customers,id'],
            'products' => ['required', 'array'],
            'products.*.productId' => ['bail','required','numeric','distinct','exists:products,id'],
            'products.*.quantity' => ['bail','required','numeric',  new StockControlRule($this->all())],
        ];
    }
}
