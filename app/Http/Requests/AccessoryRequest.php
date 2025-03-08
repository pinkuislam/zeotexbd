<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryRequest extends FormRequest
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
            'name' => 'required|max:255|string',
            'unit_id' => 'nullable|integer',
            'alert_quantity' => 'nullable|integer',
            'unit_price' => 'nullable|numeric',
            'status' => 'required|in:Active,Deactivated',
        ];
    }
}
