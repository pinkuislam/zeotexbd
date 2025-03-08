<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderBaseProductionRequest extends FormRequest
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
            'date' => 'required|date',
            'note' => 'nullable|string',
            'order_id' => 'required|integer',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'color_id' => 'nullable|array|min:1',
            'color_id.*' => 'nullable|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'fabric_unit_id' => 'required|array|min:1',
            'fabric_unit_id.*' => 'required|numeric',
            'fabric_product_id' => 'required|array|min:1',
            'fabric_product_id.*' => 'required|numeric',
            'fabric_quantity' => 'required|array|min:1',
            'fabric_quantity.*' => 'required|numeric',
            'fabric_unit_price' => 'required|array|min:1',
            'fabric_unit_price.*' => 'required|numeric',
        ];
    }
}
