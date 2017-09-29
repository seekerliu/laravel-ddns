<?php

namespace Seekerliu\DynamicDns;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DynamicDnsCommand::class,
            ]);
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('dns:sync')->everyMinute();
            });
        }
    }

    /**
     * Register the provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ddns', Dns::class);
    }
}
