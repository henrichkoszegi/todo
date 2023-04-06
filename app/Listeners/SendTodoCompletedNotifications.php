<?php

namespace App\Listeners;

use App\Events\TodoCompleted;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTodoCompletedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TodoCompleted $event): void
    {
        $user = User::find($event->todo->user_id);
        $user->notify(new \App\Notifications\TodoCompleted($event->todo));

        foreach ($event->todo->shares()->get() as $user) {
            $user->notify(new \App\Notifications\TodoCompleted($event->todo));
        }
    }
}
