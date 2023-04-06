<?php

namespace App\Providers;

use App\Events\TodoCompleted;
use App\Events\TodoShared;
use App\Listeners\SendTodoCompletedNotifications;
use App\Listeners\SendTodoSharedNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TodoCompleted::class => [
            SendTodoCompletedNotifications::class,
        ],
        TodoShared::class => [
            SendTodoSharedNotifications::class
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
