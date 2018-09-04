<?php

namespace Amranidev\Laracombee\Providers;

use Amranidev\Laracombee\Commands\DefineItemProperties;
use Amranidev\Laracombee\Commands\DefineUserProperties;
use Amranidev\Laracombee\Commands\DeleteItemProperties;
use Amranidev\Laracombee\Commands\DeleteUserProperties;
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
                DefineUserProperties::class,
                DeleteUserProperties::class,
                DefineItemProperties::class,
                DeleteItemProperties::class,
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
