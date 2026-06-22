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

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => "The orchestra name.",
                'example' => 'Los Angeles Philharmonic',
            ],
            'location' => [
                'description' => "The orchestra main location.",
                'example' => 'Los Angeles',
            ],
        ];
    }
}