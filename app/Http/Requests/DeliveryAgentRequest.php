<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryAgentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:delivery_agents,mobile,'.$this->id,
            'emergency_mobile' => 'nullable|string|max:15',
            'type' => 'required|in:Staff,Agent',
            'status' => 'required|in:Active,Deactivated',
        ];
    }
}
