<?php

namespace App\Http\Requests\Api\V1\Orchestra;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Orchestra;

class StoreOrchestraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Orchestra::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}
