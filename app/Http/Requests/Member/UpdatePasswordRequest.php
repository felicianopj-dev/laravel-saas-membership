<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
            ],
        ];
    }
    
    public function after(): array
    {
        return [
            function (): void {
                $user = $this->user();
                
                if (! $user) {
                    return;
                }
                
                if (! Hash::check((string) $this->input('current_password'), $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => 'The current password is incorrect.',
                    ]);
                }
            },
        ];
    }
    
    public function messages(): array
    {
        return [
            'current_password.required' => 'The current password field is required.',
            'password.required' => 'The new password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters.',
        ];
    }
}