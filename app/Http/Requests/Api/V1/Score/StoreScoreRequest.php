<?php

namespace App\Http\Requests\Api\V1\Score;

use App\Models\Score;
use Illuminate\Foundation\Http\FormRequest;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'file' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:10240',
            ],
        ];
    }
}