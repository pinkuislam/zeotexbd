<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RawMetrialPurchaseRequest extends FormRequest
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
            'challan_image' => 'nullable|mimes:jpg,jpeg,png',
            'order_id' => 'nullable|integer',
            'supplier_id' => 'required|integer',
            'date' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'color_id' => 'nullable|array',
            'color_id.*' => 'nullable',
            'quantity' => 'nullable|array|min:1',
            'quantity.*' => 'nullable|numeric',
            'unit_price' => 'nullable|array|min:1',
            'unit_price.*' => 'nullable|numeric',
            'amount' => 'nullable|array|min:1',
            'amount.*' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'adjust_amount' => 'nullable|numeric',
            'vat_amount' => 'nullable|numeric',
            'subtotal_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            
            /* 'fabric_unit_id' => 'nullable|array|min:1',
            'fabric_unit_id.*' => 'nullable|integer',
            'fabric_unit_price' => 'nullable|array|min:1',
            'fabric_unit_price.*' => 'nullable|numeric',
            'fabric_quantity' => 'nullable|array|min:1',
            'fabric_quantity.*' => 'nullable|numeric', */
        ];
    }
}
