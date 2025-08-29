<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
          
            'website' => ['nullable', 'size:0'],
        ];
    }
    public function messages(): array
    {
        return [
            'website.size' => 'Spam detected.'
        ];
    }
}
