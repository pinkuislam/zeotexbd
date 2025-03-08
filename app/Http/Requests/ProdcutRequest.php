<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdcutRequest extends FormRequest
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
            'product_type' => 'required|in:Fabric,Base,Base-Ready-Production,Package,Product,Combo',
            'name' => 'required|max:255',
            'category' => 'nullable|required_if:type,==,Package,Base,Base-Ready-Production|string',
            'fabric_product_id' => 'nullable|required_if:type,==,Base,Base-Ready-Production|integer',
            'fabric_unit_id' => 'nullable|required_if:type,==,Base,Base-Ready-Production|integer',
            'fabric_quantity' => 'nullable|required_if:type,==,Base,Base-Ready-Production|numeric',
            'unit_id' => 'nullable|integer',
            'alert_quantity' => 'nullable|numeric',
            'seat_count' => 'nullable|required_if:type,==,Base,Base-Ready-Production|in:1,2,3,4',
            'unit_price' => 'nullable|numeric',
            'reseller_price' => 'nullable|numeric',
            'status' => 'required|in:Active,Deactivated',
            'quantity' => 'nullable|array|min:1',
            'quantity.*' => 'nullable|numeric||required_if:type,==,Package'
        ];
    }
}
