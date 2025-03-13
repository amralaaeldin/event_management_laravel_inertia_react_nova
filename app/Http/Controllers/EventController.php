<?php

namespace App\Http\Controllers;

use App\Mail\NewEventJoin;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
        $user = Auth::user();
        $event->attendees()->syncWithoutDetaching($user->id);

        $mail = new NewEventJoin($user, $event);
        Mail::send($mail);

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
