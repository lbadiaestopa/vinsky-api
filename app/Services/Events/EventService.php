<?php

namespace App\Services\Events;

use App\Models\Program;
use App\Models\Event;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class EventService
{
    public function create(array $data, Program $program): Event
    {
        $this->assertEventInsideProgram($data, $program);

        return Event::create([
            'repertoire' => $data['repertoire'],
            'type' => $data['type'],
            'location' => $data['location'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'program_id' => $program->id,
        ]);
    }

    public function index(Program $program): Collection
    {
        return Event::query()
            ->where('program_id', $program->id)
            ->orderBy('start_date')
            ->get();
    }

    public function show(Program $program, Event $event): Event
    {
        abort_unless(
            $event->program_id === $program->id,
            404
        );

        return $event;
    }

    public function update(Event $event, array $data, Program $program): Event
    {
        $this->assertEventInsideProgram($data, $program);

        $event->update($data);

        return $event;
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }

    private function assertEventInsideProgram(array $data, Program $program): void
    {
        $errors = [];

        $start = $data['start_date'];
        $end = $data['end_date'];

        if ($start < $program->start_date) {
            $errors['start_date'][] = 'Event cannot start before program starts.';
        }

        if ($end > $program->end_date) {
            $errors['end_date'][] = 'Event cannot end after program ends.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
