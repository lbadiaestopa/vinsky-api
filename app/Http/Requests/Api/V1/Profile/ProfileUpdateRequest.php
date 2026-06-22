<?php

namespace App\Http\Requests\Api\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user()?->id),
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => "The user's first name.",
                'example' => 'Wolfgang Amadeus',
            ],
            'last_name' => [
                'description' => "The user's last name.",
                'example' => 'Mozart',
            ],
            'email' => [
                'description' => "The user's new email, must be unique.",
                'example' => 'mozart@member.com',
            ],
        ];
    }
}
