<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'type' => 'nullable|string',
            'note' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'reseller_business_id' => 'nullable|integer|exists:users,id',
            // 'shipping_rate_id' => 'nullable|integer',
            'delivery_agent_id' => 'required|integer',
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'nullable|array|min:1',
            'unit_id.*' => 'nullable|integer',
            'color_id' => 'nullable|array|min:1',
            'color_id.*' => 'nullable|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
            'vat_percent' => 'nullable|numeric',
            'vat_amount' => 'nullable|numeric',
            'shipping_charge' => 'nullable|numeric',
            'advance_amount' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
        ];
    }
}
