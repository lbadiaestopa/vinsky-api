<?php

namespace App\Http\Requests\Api\V1\Membership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],

            'orchestra_name' => [
                'required',
                'string',
                'exists:orchestras,name',
            ],

            'role' => [
                'nullable',
                Rule::in(['admin', 'member']),
            ],

            'member_type' => [
                'nullable',
                Rule::in(['core', 'substitute', 'guest']),
            ],

            'instrument' => [
                'nullable',
                'string',
                'max:255',
            ],

            'section' => [
                'nullable',
                Rule::in([
                    'violin_1',
                    'violin_2',
                    'viola',
                    'cello',
                    'double_bass',
                    'french_horn',
                    'trumpet',
                    'trombone',
                    'tuba',
                    'flute',
                    'oboe',
                    'clarinet',
                    'bassoon',
                    'percussion',
                    'mallet',
                    'vocal',
                    'other',
                ]),
            ],

            'joined_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}
