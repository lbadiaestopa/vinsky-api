<?php

namespace App\Services\Events;

use App\Models\Program;
use App\Models\Event;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function create(array $data, Program $program): Event
    {
        $start = $data['start_date'];
        $end = $data['end_date'];

        if ($start < $program->start_date) {
            throw ValidationException::withMessages([
                'start_date' => 'Event cannot start before the program period.',
            ]);
        }

        if ($end > $program->end_date) {
            throw ValidationException::withMessages([
                'end_date' => 'Event cannot end after the program period.',
            ]);
        }

        if ($start > $program->end_date || $end < $program->start_date) {
            throw ValidationException::withMessages([
                'start_date' => 'Event must be inside the program period.',
            ]);
        }

        return Event::create([
            'repertoire' => $data['repertoire'],
            'type' => $data['type'],
            'location' => $data['location'],
            'start_date' => $start,
            'end_date' => $end,
            'program_id' => $program->id,
        ]);
    }
}
