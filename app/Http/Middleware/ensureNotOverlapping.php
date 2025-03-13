<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensureNotOverlapping
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

        return $next($request);
    }
}
