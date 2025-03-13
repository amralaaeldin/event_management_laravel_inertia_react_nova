<?php

namespace App\Console\Commands;

use App\Mail\EventReminder;
use App\Mail\NewEventJoin;
use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Facades\Mail;

class NotifyUsersForEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-users-for-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting event notification process...");

        $now = Carbon::now();
        $today = $now->toDateString();

        // Get events happening today or those starting exactly at 00:00 AM
        $events = Event::whereDate('start_date_time', $today)->get();

        if ($events->isEmpty()) {
            $this->info("No events today.");
            return;
        }

        foreach ($events as $event) {
            $this->notifyUsersForEvent($event, $now);
        }

        $this->info("Event notifications completed.");
    }

    private function notifyUsersForEvent(Event $event, Carbon $now)
    {
        $eventStart = Carbon::parse($event->start_date_time);

        // If event starts at 00:00 AM, notify the day before at 23:00 PM
        if ($eventStart->format('H:i') === '00:00') {
            $notificationTime = $eventStart->copy()->subHour();
        } else {
            $notificationTime = $now;
        }

        if ($notificationTime->greaterThan($now)) {
            $this->info("Skipping notification for event '{$event->name}' as it's not time yet.");
            return;
        }

        $usersQuery = $event->attendees()->whereNotNull('email');

        // Chunk users to prevent memory overload
        $usersQuery->chunk(50, function ($users) use ($event) {
            foreach ($users as $user) {
                $mail = new EventReminder($user, $event);
                Mail::send($mail);
            }
        });

        $this->info("Notifications sent for event: {$event->name}");
    }
}
