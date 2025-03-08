<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendDyeingRequest extends FormRequest
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
            'dyeing_agent_id' => 'required|integer',
            'product_id' => 'required|integer',
            'unit_id' => 'required|numeric',
            'quantity' => 'required|numeric',
        ];
    }
}
