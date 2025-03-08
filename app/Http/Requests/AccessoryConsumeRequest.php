<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryConsumeRequest extends FormRequest
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
            'note' => 'nullable|string|max:255',
            'accessory_id' => 'required|array|min:1',
            'accessory_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
            'total_quantity' => 'required|integer',
            'subtotal_amount' => 'required|numeric',
        ];
    }
}
