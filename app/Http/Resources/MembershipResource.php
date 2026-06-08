<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],

            'orchestra' => [
                'id' => $this->orchestra->id,
                'name' => $this->orchestra->name,
            ],

            'role' => $this->role,

            'member_type' => $this->member_type,

            'instrument' => $this->instrument,

            'section' => $this->section,

            'joined_at' => $this->joined_at,

            'created_at' => $this->created_at,
        ];
    }
}