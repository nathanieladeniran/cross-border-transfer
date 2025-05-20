<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserSettingRequest extends FormRequest
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
            'current_password' => [
            'required',
            'min:8', 
            function ($attribute, $value, $fail) {
                if (!Hash::check($value, current_user()->password)) {
                    $fail(__('The provided password is incorrect.'));
                }
            }],
            'new_password' => 'required|min:8|different:current_password',
        ];
    }

    public function messages()
    {
        return [
            'current_password.min' => 'The current password must be at least 8 characters.',
            'new_password.different' => 'The new password cannot be the same as the old password.',
            'new_password.min' => 'The new password must be at least 8 characters.',
        ];
    }
}
