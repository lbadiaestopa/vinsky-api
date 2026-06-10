<?php

namespace App\Http\Requests\Api\V1\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $program = $this->route('program');
        $event = $this->route('event');

        if ($event->program_id !== $program->id) {
            abort(404);
        }

        return $this->user()->can('update', $event);
    }

    public function rules(): array
    {
        return [
            'repertoire' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rehearsal,concert,soundcheck'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
