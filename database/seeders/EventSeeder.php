<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::factory(20)->create();

        $events = Event::all();

        $users = User::role('user')->get();

        $events->each(function ($event) use ($users) {
            $event->attendees()->attach($users->random(rand(1, $users->count()))->pluck('id'));
        });
    }
}
