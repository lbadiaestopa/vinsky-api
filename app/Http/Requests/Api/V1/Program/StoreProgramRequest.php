<?php

namespace App\Http\Requests\Api\V1\Program;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Program;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(
            'create',
            [Program::class, $this->route('orchestra')]
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
