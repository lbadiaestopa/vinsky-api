<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => "The user's first name.",
                'example' => 'Ludwig',
            ],
            'last_name' => [
                'description' => "The user's last name.",
                'example' => 'van Beethoven',
            ],
            'email' => [
                'description' => "The user's email, cannot create more than 1 account with the same email.",
                'example' => 'beethoven@admin.com',
            ],
            'password' => [
                'example' => 'password',
            ],
        ];
    }
}
