<?php

namespace App\Http\Requests\Api\V1\Orchestra;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrchestraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}