<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensureFullAndWaitlistCapacity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');
        $user = $request->user();

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

        return $next($request);
    }
}
