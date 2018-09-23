<?php

namespace Amranidev\Laracombee\Providers;

use Amranidev\Laracombee\Commands\AddColumns;
use Amranidev\Laracombee\Commands\DropColumns;
use Amranidev\Laracombee\Commands\Migrate;
use Amranidev\Laracombee\Commands\Rollback;
use Amranidev\Laracombee\Laracombee;
use Illuminate\Support\ServiceProvider;

class LaracombeeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Laracombee', function () {
            return new Laracombee();
        });

        $this->commands(
            [
                Migrate::class,
                Rollback::class,
                AddColumns::class,
                DropColumns::class,
            ]
        );
    }

    public function boot()
    {
        $configPath = __DIR__ . '/../config/laracombee.php';
        $this->publishes([
            $configPath => config_path('laracombee.php')]);
    }
}
