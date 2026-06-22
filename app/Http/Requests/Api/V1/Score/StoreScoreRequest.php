<?php

namespace App\Http\Requests\Api\V1\Score;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:10240',
            ],
            'original_name' => ['nullable', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        $originalName = $this->input('original_name')
            ?? $this->file('file')?->getClientOriginalName()
            ?? '';

        $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $originalName);

        if (str_starts_with($sanitized, '.')) {
            throw new HttpResponseException(
                response()->json(['message' => 'Filenames cannot start with a dot.'], 400)
            );
        }

        if (substr_count($sanitized, '.') > 1) {
            throw new HttpResponseException(
                response()->json(['message' => "Only one dot allowed in the filename."], 400)
            );
        }

        $this->merge(['sanitized_original_name' => $sanitized]);
    }

    public function bodyParameters(): array
    {
        return [
            'file' => [
                'description' => 'The PDF file containing the musical score.',
            ],

            'original_name' => [
                'description' => 'The original filename of the uploaded score.',
                'example' => 'Beethoven_Symphony_No_5.pdf',
            ],
        ];
    }
}
