<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensurePublished
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        $isPublished = $event->status === strtolower(Event::STATUSES['published']);
        if (!$isPublished) {
            return redirect()->route('calendar.index')
                ->with('error', 'Event is not published.');
        }

        return $next($request);
    }
}
