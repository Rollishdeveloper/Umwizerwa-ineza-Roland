<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', Rule::unique(User::class)->ignore($user->id)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        if ($user->isStudent()) {
            $rules['gender'] = ['nullable', 'string', 'in:male,female,other'];
            $rules['date_of_birth'] = ['nullable', 'date', 'before:today'];
            $rules['address'] = ['nullable', 'string', 'max:500'];
        }

        if ($user->isInstructor()) {
            $rules['specialization'] = ['nullable', 'string', 'max:255'];
            $rules['biography'] = ['nullable', 'string', 'max:2000'];
        }

        return $rules;
    }
}
