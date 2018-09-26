<?php

namespace Amranidev\Laracombee\Providers;

use Amranidev\Laracombee\Console\Commands\AddColumnsCommand;
use Amranidev\Laracombee\Console\Commands\DropColumnsCommand;
use Amranidev\Laracombee\Console\Commands\MigrateCommand;
use Amranidev\Laracombee\Console\Commands\RollbackCommand;
use Amranidev\Laracombee\Console\Commands\SeedCommand;
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
                MigrateCommand::class,
                RollbackCommand::class,
                AddColumnsCommand::class,
                DropColumnsCommand::class,
                SeedCommand::class,
            ]
        );
    }

    public function boot()
    {
        $configPath = __DIR__.'/../config/laracombee.php';
        $this->publishes([
            $configPath => config_path('laracombee.php'), ]);
    }
}
