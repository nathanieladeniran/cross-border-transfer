<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserPhoneSettingRequest extends FormRequest
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
            'current_password' => ['required', function ($attribute, $value, $fail) {
            if (!Hash::check($value, current_user()->password)) {
                $fail('Invalid Password');
            }}],
            'phone' => ['required','unique:App\Models\Profile,mobile_phone']
        ];
    }

    public function messages()
    {
        return [
            'current_password.min' => 'The current password must be at least 8 characters.',
            'phone.required' => 'Phone number is required'
        ];
    }
}
