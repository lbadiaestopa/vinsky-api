<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Orchestra;
use App\Policies\OrchestraPolicy;

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
        //
    }

    protected $policies = [
        Orchestra::class => OrchestraPolicy::class,
    ];
}
