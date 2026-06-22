<?php

namespace App\Http\Requests\Api\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            if (! Hash::check($this->current_password, $user->password)) {
                $validator->errors()->add(
                    'current_password',
                    'The current password is incorrect.'
                );
            }
        });
    }

    public function bodyParameters(): array
    {
        return [
            'current_password' => [
                'example' => 'securePassword123',
            ],
            'password' => [
                'example' => 'newSecurePassword123',
            ],
        ];
    }
}
