<?php

namespace App\Listeners;

use App\Events\TodoShared;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTodoSharedNotifications implements ShouldQueue
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
    public function handle(TodoShared $event): void
    {
        foreach ($event->todo->shares()->get() as $user) {
            $user->notify(new \App\Notifications\TodoShared($event->todo));
        }
    }
}
