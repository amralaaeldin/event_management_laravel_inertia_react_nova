<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensureNotPast
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        $isUpcoming = $event->start_date_time->isFuture();
        if (!$isUpcoming) {
            return redirect()->route('calendar.index')
                ->with('error', 'Event is in the past.');
        }

        return $next($request);
    }
}
