<?php

namespace App\Http\Requests\Api\V1\Membership;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['sometimes', 'filled', 'in:admin,member'],
            'member_type' => ['sometimes', 'filled', 'in:core,substitute,guest'],
            'instrument' => ['sometimes', 'filled', 'string'],
            'section' => ['sometimes', 'filled', 'in:violin_1,violin_2,viola,cello,double_bass,french_horn,trumpet,trombone,tuba,flute,oboe,clarinet,bassoon,percussion,mallet,vocal,other'],
        ];
    }
}
