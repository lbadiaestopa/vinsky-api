<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Orchestra;
use App\Policies\OrchestraPolicy;
use App\Models\Program;
use App\Policies\ProgramPolicy;
use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Support\Facades\Gate;

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
        Gate::policy(Event::class, EventPolicy::class);
    }
}
