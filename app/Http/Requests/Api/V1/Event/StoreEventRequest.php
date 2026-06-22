<?php

namespace App\Http\Requests\Api\V1\Event;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Event;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $program = $this->route('program');
        
        return $this->user()->can('create', [Event::class, $program]);
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

    public function bodyParameters(): array
    {
        return [
            'repertoire' => [
                'description' => 'The repertoire to be played in an event.',
                'example' => 'Beethoven Symphony 5',
            ],
            'type' => [
                'description' => 'The type of event.',
                'example' => 'Rehearsal',
            ],
            'location' => [
                'description' => 'The location of the event.',
                'example' => 'Hollywood Bowl',
            ],
            'start_date' => [
                'example' => '2026-07-14',
            ],
            'end_date' => [
                'example' => '2026-07-21',
            ],
        ];
    }
}
