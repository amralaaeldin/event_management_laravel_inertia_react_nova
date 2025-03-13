<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensureNotAttending
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        $isAttending = $event->attendees->contains($request->user()->id);
        if ($isAttending) {
            return redirect()->route('calendar.index')
                ->with('error', 'You are already attending this event.');
        }

        return $next($request);
    }
}
