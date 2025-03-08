<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleReturnRequest extends FormRequest
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
            'note' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'reseller_business_id' => 'nullable|integer|exists:users,id',
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'nullable|array|min:1',
            'unit_id.*' => 'nullable|integer',
            'color_id' => 'nullable|array|min:1',
            'color_id.*' => 'nullable|integer',
            'quantity' => 'nullable|array',
            'quantity.*' => 'nullable|numeric',
            'unit_price' => 'nullable|array',
            'unit_price.*' => 'nullable|numeric',
            'amount' => 'nullable|array',
            'amount.*' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'deduction_amount' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
        ];
    }
}
