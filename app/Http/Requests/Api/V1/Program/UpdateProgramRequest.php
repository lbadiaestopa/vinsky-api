<?php

namespace App\Http\Requests\Api\V1\Program;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'update',
            $this->route('program')
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => "The program name.",
                'example' => 'Beethoven Cycle 2026',
            ],
            'start_date' => [
                'example' => '2026-07-14',
            ],
            'end_date' => [
                'example' => '2026-07-21',
            ],
        ];
    }
}
