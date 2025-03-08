<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryStockReturnRequest extends FormRequest
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
            'supplier_id' => 'required|integer',
            'purchase_id' => 'required|integer',
            'accessory_id' => 'required|array|min:1',
            'accessory_id.*' => 'required|integer',
            'quantity' => 'nullable|array',
            'quantity.*' => 'nullable|numeric',
            'unit_price' => 'nullable|array',
            'unit_price.*' => 'nullable|numeric',
            'amount' => 'nullable|array',
            'amount.*' => 'nullable|numeric',
            'subtotal_amount' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'total_amount' => 'required|numeric'
        ];
    }
}
