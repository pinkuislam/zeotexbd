<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiOrderRequest extends FormRequest
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
            'note' => 'required|string',

            'items' => 'required|array',
            'items.*' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.color_id' => 'nullable|integer',
            'items.*.unit_price' => 'required|numeric',
            'items.*.amount' => 'required|numeric',
        ];
    }
}
