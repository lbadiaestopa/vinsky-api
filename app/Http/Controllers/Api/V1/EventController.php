<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Event\StoreEventRequest;
use App\Http\Requests\Api\V1\Event\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Program;
use App\Models\Event;
use App\Services\Events\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
* @group Events
* Endpoints for managing events. Events belong to a program.
*/
class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $service
    ) {}

    /**
     * Create an event
     * 
     * User must have a membership linked to the orchestra containing the orchestra with an admin role to create it. 
     */
    public function store(StoreEventRequest $request, Program $program)
    {
        $event = $this->service->create($request->validated(), $program);

        return new EventResource($event);
    }

    /**
     * List all events
     * 
     * User must have a membership linked to the orchestra containing the event to see them. 
     */
    public function index(Program $program)
    {
        $this->authorize('viewAll', [Event::class, $program]);

        $events = $this->service->index($program);

        return EventResource::collection($events);
    }

    /**
     * Get an event
     * 
     * User must have a membership linked to the orchestra containing the orchestra to see it. 
     */
    public function show(Program $program, Event $event)
    {
        if ($event->program_id !== $program->id) {
            abort(404);
        }

        $this->authorize('view', $event);

        return new EventResource($event);
    }

    /**
     * Update an event
     * 
     * User must have a membership linked to the orchestra containing the orchestra with an admin role to update it. 
     */
    public function update(UpdateEventRequest $request, Program $program, Event $event)
    {
        $updated = $this->service->update($event, $request->validated(), $program);

        return new EventResource($updated);
    }

    /**
     * Delete an event
     * 
     * User must have a membership linked to the orchestra containing the orchestra with an admin role to delete it. 
     */
    public function destroy(Program $program, Event $event)
    {
        if ($event->program_id !== $program->id) {
            abort(404);
        }
        
        $this->authorize('delete', [$event, $program]);

        $this->service->delete($event);

        return response()->noContent();
    }
}
