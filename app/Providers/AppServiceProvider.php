<?php

namespace App\Providers;

use App\Events\ActionNeededReportEvent;
use App\Events\ActionNeededRequestEvent;
use App\Events\ConfirmationReportEvent;
use App\Events\ConfirmationRequestEvent;
use App\Events\RejectReportEvent;
use App\Events\RejectRequestEvent;
use App\Listeners\RequestListener;
use App\Models\File;
use App\Models\PersonalAccessToken;
use App\Observers\FileObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        File::observe(FileObserver::class);
        Event::listen(
            [
                ActionNeededRequestEvent::class,
                ConfirmationRequestEvent::class,
                RejectRequestEvent::class,

                ActionNeededReportEvent::class,
                ConfirmationReportEvent::class,
                RejectReportEvent::class,
            ],
            RequestListener::class
        );
    }
}
