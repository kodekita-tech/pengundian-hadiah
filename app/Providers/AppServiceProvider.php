<?php

namespace App\Providers;

use App\Models\Event;
use App\Observers\EventObserver;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set default timezone untuk Carbon
        Carbon::setLocale('id');
        date_default_timezone_set(config('app.timezone'));

        // Register Event Observer for auto-update status based on date
        Event::observe(EventObserver::class);
    }
}
