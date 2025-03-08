<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,id',
            'shipping_charge' => 'nullable|numeric',
            'advance_amount' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'bank_id' => 'nullable|numeric',
            'delivery_agent_id' => 'nullable|numeric',
            'order_amount' => 'nullable|numeric',
            'product_id' => 'required|array',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric',
            'color_id' => 'nullable|array',
            'color_id.*' => 'nullable|numeric',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
        ];
    }
}
