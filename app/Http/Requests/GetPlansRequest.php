<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPlansRequest extends FormRequest
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
            'cycle' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'cycle.required' => 'The cycle is required in order to get plans.',
        ];
    }

}