<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
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
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'color_id' => 'nullable|array|min:1',
            'color_id.*' => 'nullable|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
        ];
    }
}
