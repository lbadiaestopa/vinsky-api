<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Event\StoreEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Program;
use App\Models\Event;
use App\Services\Events\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $service
    ) {}

    public function store(StoreEventRequest $request, Program $program)
    {
        $event = $this->service->create($request->validated(), $program);

        return new EventResource($event);
    }

    public function index(Program $program)
    {
        $this->authorize('viewAll', [Event::class, $program]);

        $events = $this->service->index($program);

        return EventResource::collection($events);
    }
}
