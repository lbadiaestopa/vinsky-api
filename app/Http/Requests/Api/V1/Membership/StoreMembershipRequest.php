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

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => "The email address of the user to add to the orchestra. The user must already exist.",
                'example' => 'beethoven@example.com',
            ],

            'orchestra_name' => [
                'description' => 'The orchestra name.',
                'example' => 'Barcelona Symphony Orchestra',
            ],

            'role' => [
                'description' => 'The member role within the orchestra.',
                'example' => 'member',
            ],

            'member_type' => [
                'description' => 'The type of membership.',
                'example' => 'core',
            ],

            'instrument' => [
                'description' => 'The member instrument.',
                'example' => 'Violin',
            ],

            'section' => [
                'description' => 'The orchestra section assigned to the member.',
                'example' => 'violin_1',
            ],

            'joined_at' => [
                'description' => 'The date when the member joined the orchestra.',
                'example' => '2026-06-22',
            ],
        ];
    }
}
