<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PostHog\PostHog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!config('services.posthog.enabled', false) || !config('services.posthog.api_key')) {
            return;
        }

        PostHog::init(
            config('services.posthog.api_key'),
            [
                'host' => config('services.posthog.host'),
            ]
        );
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
