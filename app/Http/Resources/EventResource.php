<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'program_id' => $this->program_id,

            'repertoire' => $this->repertoire,
            'type' => $this->type,
            'location' => $this->location,

            'start_date' => $this->start_date,
            'end_date' => $this->end_date,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}