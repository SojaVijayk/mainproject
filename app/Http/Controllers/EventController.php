<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventType;
use App\Models\EventMode;
use App\Models\VenueType;
use App\Models\Venue;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventConfirmation;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
       $pageConfigs = ['myLayout' => 'horizontal'];
        return view('calendar',['pageConfigs'=> $pageConfigs]);
    }

    public function getEvents()
{
    $events = Event::with([
            'eventType',
            'eventMode',
            'venueType',
            'venues',
            'coordinators',
            'faculties',
            'creator'
        ])
        ->get()
        ->map(function ($event) {
            $canEdit = auth()->user()->id === $event->user_id ||
                      $event->coordinators->contains(auth()->id()) ||
                      ($event->faculties && $event->faculties->contains(auth()->id()));

            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start' => $event->start_date,
                'end' => $event->end_date,
                'eventType' => $event->eventType->name,
                'eventMode' => $event->eventMode->name,
                'coordinators' => $event->coordinators->pluck('name'),
                'faculties' => $event->faculties->pluck('name'),
                'participants_count' => $event->participants_count,
                'event_category' => $event->event_category,
                'external_entity' => $event->external_entity,
                'venue_type' => $event->venueType->name,
                'venues' => $event->venues->pluck('name'),
                'custom_amenities_request' => $event->custom_amenities_request,
                'creator' => $event->creator->name,
                'created_at' => $event->created_at->format('M j, Y g:i A'),
                'canEdit' => $canEdit,
                'color' => $canEdit ? null : '#6c757d'
            ];
        });

    return response()->json($events);
}

    public function store(Request $request)
    {
        $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'event_type_id' => 'required|exists:event_types,id',
        'event_mode_id' => 'required|exists:event_modes,id',
        'coordinators' => 'nullable|array',
        'coordinators.*' => 'exists:users,id',
        'faculties' => 'nullable|array',
        'faculties.*' => 'exists:users,id',
        'participants_count' => 'nullable|integer|min:0',
        'event_category' => 'required|in:CMD,External',
        'external_entity' => 'nullable|required_if:event_category,External|string|max:255',
        'venue_type_id' => 'required|exists:venue_types,id',
        'venues' => 'required_if:venue_type_id,1,3|array',
        'venues.*' => 'exists:venues,id',
        'custom_amenities_request' => 'nullable|string',
        'external_venue' => 'nullable|required_if:venue_type_id,2|string|max:255',
    ]);

         // Check venue availability
    if ($request->venues) {
        $conflicts = $this->checkVenueAvailability($request->venues, $request->start_date, $request->end_date);

        if (!empty($conflicts)) {
            return response()->json([
                'error' => 'The following venues are already booked during this time: ' . implode(', ', $conflicts)
            ], 422);
        }
    }


 // Create event
    $eventData = $request->except(['coordinators', 'faculties', 'venues']);
    $eventData['user_id'] = auth()->id();
    $event = Event::create($eventData);

    // Attach coordinators
    if ($request->coordinators) {
    $event->coordinators()->sync($request->coordinators);
    }

    // Attach faculties if provided
    if ($request->faculties) {
        $event->faculties()->sync($request->faculties);
    }

     // Attach venues with custom amenities
    if ($request->venues) {
        $venuesWithAmenities = [];
        foreach ($request->venues as $venueId) {
            $venuesWithAmenities[$venueId] = ['custom_amenities' => $request->custom_amenities_request];
        }
        $event->venues()->sync($venuesWithAmenities);
    }

// Send confirmation emails to all coordinators
if ($request->coordinators) {
    foreach ($event->coordinators as $coordinator) {
        Mail::to($coordinator->email)->send(new EventConfirmation($event));
    }
  }
  if ($request->faculties) {
    foreach ($event->faculties as $facultie) {
        Mail::to($facultie->email)->send(new EventConfirmation($event));
    }
  }

        return response()->json($event);
    }

    // Add new method to get available venues
public function getAvailableVenuesForTime(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);
    $excludeEventId = $request->input('exclude_event');

    // Get all active venues
    $allVenues = Venue::where('status', 'active')
        ->where('is_active', true)
        ->get();

    // Get venues that are already booked during this time (excluding current event if editing)
    $query = Event::where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($query) use ($startDate, $endDate) {
                      $query->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                  });
        })
        ->whereHas('venues');

    if ($excludeEventId) {
        $query->where('id', '!=', $excludeEventId);
    }

    $bookedVenues = $query->get()
        ->flatMap(function ($event) {
            return $event->venues->pluck('id');
        })
        ->unique()
        ->toArray();

    // Filter available venues
    $availableVenues = $allVenues->reject(function ($venue) use ($bookedVenues) {
        return in_array($venue->id, $bookedVenues);
    });

    return response()->json($availableVenues->values());
}

        // Add these methods to check authorization
    private function canEditEvent(Event $event)
    {
        return auth()->user()->id === $event->coordinator_id ||
              auth()->user()->id === $event->faculty_id ||
              auth()->user()->id === $event->user_id; // Assuming you have user_id as creator
    }
   public function show(Event $event)
{
    $event->load([
        'eventType',
        'eventMode',
        'venueType',
        'venues',
        'coordinators' => function($query) {
            $query->select('users.id', 'users.name');
        },
        'faculties' => function($query) {
            $query->select('users.id', 'users.name');
        },
        'creator'
    ]);

    return response()->json([
        'id' => $event->id,
        'title' => $event->title,
        'description' => $event->description,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
        'event_type_id' => $event->event_type_id,
        'event_type' => $event->eventType->name,
        'event_mode_id' => $event->event_mode_id,
        'event_mode' => $event->eventMode->name,
        'coordinators' => $event->coordinators,
        'faculties' => $event->faculties,
        'participants_count' => $event->participants_count,
        'event_category' => $event->event_category,
        'external_entity' => $event->external_entity,
        'venue_type_id' => $event->venue_type_id,
        'venue_type' => $event->venueType->name,
        'venues' => $event->venues->map(function($venue) {
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'custom_amenities' => $venue->pivot->custom_amenities
            ];
        }),
        'custom_amenities_request' => $event->custom_amenities_request,
        'external_venue' => $event->external_venue,
        'creator' => $event->creator->only(['id', 'name']),
        'created_at' => $event->created_at->format('M j, Y g:i A'),
        'can_edit' => $this->canEditEvent($event)
    ]);
}

    public function update(Request $request, Event $event)
{
    if (!$this->canEditEvent($event)) {
        return response()->json(['error' => 'Unauthorized to edit this event'], 403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'event_type_id' => 'required|exists:event_types,id',
        'event_mode_id' => 'required|exists:event_modes,id',
        'coordinators' => 'nullable|array',
        'coordinators.*' => 'exists:users,id',
        'faculties' => 'nullable|array',
        'faculties.*' => 'exists:users,id',
        'participants_count' => 'nullable|integer|min:0',
        'event_category' => 'required|in:CMD,External',
        'external_entity' => 'nullable|required_if:event_category,External|string|max:255',
        'venue_type_id' => 'required|exists:venue_types,id',
        'venues' => 'required_if:venue_type_id,1,3|array',
        'venues.*' => 'exists:venues,id',
        'custom_amenities_request' => 'nullable|string',
        'external_venue' => 'nullable|required_if:venue_type_id,2|string|max:255',
    ]);

    // Check venue availability (excluding current event)
    if ($request->venues) {
        $conflicts = $this->checkVenueAvailability($request->venues, $request->start_date, $request->end_date, $event->id);

        if (!empty($conflicts)) {
            return response()->json([
                'error' => 'The following venues are already booked during this time: ' . implode(', ', $conflicts)
            ], 422);
        }
    }

    // Update basic event data
    $event->update($request->except(['coordinators', 'faculties', 'venues']));

    // Sync coordinators
    $event->coordinators()->sync($request->coordinators);

    // Sync faculties if provided
    $event->faculties()->sync($request->faculties ?? []);

    // Sync venues with custom amenities
    if ($request->venues) {
        $venuesWithAmenities = [];
        foreach ($request->venues as $venueId) {
            $venuesWithAmenities[$venueId] = ['custom_amenities' => $request->custom_amenities_request];
        }
        $event->venues()->sync($venuesWithAmenities);
    } else {
        $event->venues()->detach();
    }

    // Send confirmation emails to all coordinators
    // foreach ($event->coordinators as $coordinator) {
    //     Mail::to($coordinator->email)->send(new EventConfirmation($event));
    // }

    // Send confirmation emails to all coordinators
if ($request->coordinators) {
    foreach ($event->coordinators as $coordinator) {
        Mail::to($coordinator->email)->send(new EventConfirmation($event));
    }
  }
  if ($request->faculties) {
    foreach ($event->faculties as $facultie) {
        Mail::to($facultie->email)->send(new EventConfirmation($event));
    }
  }


    return response()->json($event->load(['eventType', 'eventMode', 'venues', 'coordinators', 'faculties']));
}

    public function destroy(Event $event)
    {
       if (!$this->canEditEvent($event)) {
        return response()->json(['error' => 'Unauthorized to delete this event'], 403);
      }
        $event->delete();
        return response()->json(null, 204);
    }

    public function getFormData()
    {
        $eventTypes = EventType::where('is_active', true)->get();
        $eventModes = EventMode::where('is_active', true)->get();
        $venueTypes = VenueType::where('is_active', true)->get();
        $venues = Venue::where('is_active', true)->where('status', 'active')->get();
        $users = User::where('active',1)->get();

        return response()->json([
            'eventTypes' => $eventTypes,
            'eventModes' => $eventModes,
            'venueTypes' => $venueTypes,
            'venues' => $venues,
            'users' => $users,
        ]);
    }

    private function checkVenueAvailability($venueIds, $startDate, $endDate, $excludeEventId = null)
{
    $conflicts = [];

    foreach ($venueIds as $venueId) {
        $query = Event::whereHas('venues', function($q) use ($venueId) {
                $q->where('venue_id', $venueId);
            })
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<', $startDate)
                                ->where('end_date', '>', $endDate);
                      });
            });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        if ($query->exists()) {
            $venue = Venue::find($venueId);
            $conflicts[] = $venue->name;
        }
    }

    return $conflicts;
}

private function getAvailableVenues($startDate, $endDate)
{
    $bookedVenueIds = Event::where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($query) use ($startDate, $endDate) {
                      $query->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                  });
        })
        ->whereHas('venues')
        ->pluck('id');

    return Venue::where('status', 'active')
        ->where('is_active', true)
        ->whereNotIn('id', function($query) use ($startDate, $endDate) {
            $query->select('venue_id')
                ->from('event_venue')
                ->join('events', 'events.id', '=', 'event_venue.event_id')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function($query) use ($startDate, $endDate) {
                              $query->where('start_date', '<', $startDate)
                                    ->where('end_date', '>', $endDate);
                          });
                });
        })
        ->get();
}


}