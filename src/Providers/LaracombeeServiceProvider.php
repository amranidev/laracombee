<?php

namespace Amranidev\Laracombee\Providers;

use Amranidev\Laracombee\Laracombee;
use Illuminate\Support\ServiceProvider;
use Amranidev\Laracombee\Console\Commands\SeedCommand;
use Amranidev\Laracombee\Console\Commands\MigrateCommand;
use Amranidev\Laracombee\Console\Commands\RollbackCommand;
use Amranidev\Laracombee\Console\Commands\AddColumnsCommand;
use Amranidev\Laracombee\Console\Commands\DropColumnsCommand;
use Amranidev\Laracombee\Console\Commands\ResetDatabaseCommand;
use Amranidev\Laracombee\Console\Commands\CreateNewLaracombeeClass;

class LaracombeeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Laracombee', function () {
            return new Laracombee();
        });
    }

    /**
     * Seriveprovider's boot method.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../config/laracombee.php';
        $this->publishes([
            $configPath => base_path('config/laracombee.php'), ]);

        $this->commands(
            [
                SeedCommand::class,
                MigrateCommand::class,
                RollbackCommand::class,
                AddColumnsCommand::class,
                DropColumnsCommand::class,
                ResetDatabaseCommand::class,
                CreateNewLaracombeeClass::class,
            ]
        );
    }
}
