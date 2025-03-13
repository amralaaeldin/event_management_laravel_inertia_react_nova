<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date', now()->format('Y-m-d'));
        $view = $request->input('view', 'week');

        $query = Event::published()->with('attendees:id', 'wishlistUsers:id');

        // Filter events based on selected view
        if ($view === "month") {
            $query->whereMonth('start_date_time', date('m', strtotime($date)))
                ->whereYear('start_date_time', date('Y', strtotime($date)));
        } elseif ($view === "week") {
            $startOfWeek = date('Y-m-d', strtotime('last sunday', strtotime($date)));
            $endOfWeek = date('Y-m-d', strtotime('saturday this week', strtotime($date)));
            $query->whereBetween('start_date_time', [$startOfWeek, $endOfWeek]);
        } else { // Day View
            $query->whereDate('start_date_time', $date);
        }

        $events = $query->get();

        $events = $events->map(function ($event) use ($userId) {
            $event->is_attending = $event->attendees->contains('id', $userId);
            $event->is_wishlist = $event->wishlistUsers->contains('id', $userId);
            return $event;
        });

        return Inertia::render('Calendar', [
            'events' => $events,
            'filters' => ['date' => $date, 'view' => $view],
        ]);
    }

    public function attend(Event $event)
    {
        $isPublished = $event->status === 'published';
        if (!$isPublished) {
            return redirect()->route('calendar.index')
                ->with('error', 'Event is not published.');
        }

        $user = Auth::user();

        $isAttending = $event->attendees->contains($user->id);
        if ($isAttending) {
            return redirect()->route('calendar.index')
                ->with('error', 'You are already attending this event.');
        }

        $isIncoming = $event->start_date_time->isFuture();
        if (!$isIncoming) {
            return redirect()->route('calendar.index')
                ->with('error', 'Event is in the past.');
        }

        $isFull = $event->attendees->count() >= $event->capacity;
        $isInWaitlist = $event->wishlistUsers->contains($user->id);
        if ($isFull && !$isInWaitlist) {
            $isWaitlistFull = $event->wishlistUsers->count() >= $event->waitlist_capacity;
            if ($isWaitlistFull) {
                return redirect()->route('calendar.index')
                    ->with('error', 'Both Event & Waitlist are full.');
            }

            $event->wishlistUsers()->syncWithoutDetaching($user->id);
            return redirect()->route('calendar.index')
                ->with('success', 'Event is full and You have been added to the waitlist.');
        }

        // ensure no overlap between the event and the user attendance events
        $isOverlap = $user->attendedEvents()
            ->where('start_date_time', '>', now())
            ->whereRaw(
                "TIMESTAMPADD(MINUTE, duration, start_date_time) > ? AND start_date_time < TIMESTAMPADD(MINUTE, ?, ?)",
                [$event->start_date_time, $event->duration, $event->start_date_time]
            )->exists();
        if ($isOverlap) {
            return redirect()->route('calendar.index')
                ->with('error', 'You are already attending an Event in this time.');
        }

        $event->attendees()->syncWithoutDetaching($user->id);

        return redirect()->route('calendar.index')
            ->with('success', 'You are now attending the event.');
    }

    public function unattend(Event $event)
    {
        $event->attendees()->detach(Auth::id());
        $event->wishlistUsers()->detach(Auth::id());

        return redirect()->route('calendar.index')
            ->with('success', 'You are no longer attending the event.');
    }
}
