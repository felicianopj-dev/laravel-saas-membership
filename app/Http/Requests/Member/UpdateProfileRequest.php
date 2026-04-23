<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can update their own profile
        return auth()->check();
    }
    
    public function rules(): array
    {
        $user = $this->user();
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Ignore current user's email for uniqueness
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',
        ];
    }
}