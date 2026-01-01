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
use Illuminate\Database\Eloquent\Relations\Relation;
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
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
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

        Relation::enforceMorphMap([
            'area' => 'App\Models\Area',
            'banner' => 'App\Models\Banner',
            'city' => 'App\Models\City',
            'comment' => 'App\Models\Comment',
            'dashboard_item' => 'App\Models\DashboardItem',
            'file' => 'App\Models\File',
            'form' => 'App\Models\Form',
            'form_item' => 'App\Models\FormItem',
            'form_report' => 'App\Models\FormReport',
            'log_activity' => 'App\Models\LogActivity',
            'neighborhood' => 'App\Models\Neighborhood',
            'personal_access_token' => 'App\Models\PersonalAccessToken',
            'region' => 'App\Models\Region',
            'report' => 'App\Models\Report',
            'request' => 'App\Models\Request',
            'request_plan' => 'App\Models\RequestPlan',
            'request_plan_limit' => 'App\Models\RequestPlanLimit',
            'ring' => 'App\Models\Ring',
            'ring_member' => 'App\Models\RingMember',
            'settings' => 'App\Models\Settings',
            'statistic' => 'App\Models\Statistic',
            'unit' => 'App\Models\Unit',
            'user' => 'App\Models\User',
            'user_role' => 'App\Models\UserRole',
            'written_request' => 'App\Models\WrittenRequest',
            'camp_ticket' => 'App\Models\CampTicket',
        ]);
    }
}
