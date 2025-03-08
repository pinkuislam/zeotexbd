<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'note' => 'nullable|string',
            'bank_id' => 'required|array|min:1',
            'bank_id.*' => 'required|integer',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|integer',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ];
    }
}
