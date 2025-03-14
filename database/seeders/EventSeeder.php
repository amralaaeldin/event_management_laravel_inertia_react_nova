<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::factory(50)->create();

        $events = Event::all();

        $users = User::role('user')->get();

        // Split users into two groups: attendees and waitlist users
        $shuffledUsers = $users->shuffle();
        $attendeesCount = floor(count($users) / 2);
        $attendees = $shuffledUsers->take($attendeesCount);
        $wishlistUsers = $shuffledUsers->slice($attendeesCount);

        $events->each(function (Event $event) use ($attendees, $wishlistUsers) {
            $selectedAttendees = $attendees->random(rand(1, $attendees->count()))->pluck('id');
            $event->attendees()->syncWithoutDetaching($selectedAttendees);

            $waitlistSelection = $wishlistUsers->random(rand(1, $wishlistUsers->count()))
                ->pluck('id')
                ->toArray();
            $event->wishlistUsers()->syncWithoutDetaching($waitlistSelection);
        });

        // Ensure no duplicates between attendees & waitlist
        $attendees = DB::table('event_user_attendance')->get();

        foreach ($attendees as $attendee) {
            DB::table('event_user_wishlist')
                ->where('user_id', $attendee->user_id)
                ->where('event_id', $attendee->event_id)
                ->delete();
        }
    }
}
