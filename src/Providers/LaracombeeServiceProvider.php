<?php

namespace Amranidev\Laracombee\Providers;

use Illuminate\Support\ServiceProvider;
use Amranidev\Laracombee\Laracombee;
use Amranidev\Laracombee\ModelMapper;
use Amranidev\Laracombee\LaracombeeConnector;
use Amranidev\Laracombee\Console\Commands\SeedCommand;
use Amranidev\Laracombee\Console\Commands\MigrateCommand;
use Amranidev\Laracombee\Console\Commands\RollbackCommand;
use Amranidev\Laracombee\Console\Commands\AddColumnsCommand;
use Amranidev\Laracombee\Console\Commands\DropColumnsCommand;
use Amranidev\Laracombee\Console\Commands\ResetDatabaseCommand;
use Amranidev\Laracombee\Console\Commands\CreateNewLaracombeeClass;

/**
 * Register the package client, configuration publishing, and Artisan commands.
 */
class LaracombeeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laracombee.php', 'laracombee');

        $this->app->singleton(ModelMapper::class);
        $this->app->alias('laracombee', Laracombee::class);

        $this->app->singleton('laracombee', function ($app) {
            return $app->make(LaracombeeConnector::class)->connect(
                $app['config']->get('laracombee'),
                $app->make(ModelMapper::class)
            );
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../config/laracombee.php';
        $this->publishes([
            $configPath => config_path('laracombee.php'),
        ], 'laracombee-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedCommand::class,
                MigrateCommand::class,
                RollbackCommand::class,
                AddColumnsCommand::class,
                DropColumnsCommand::class,
                ResetDatabaseCommand::class,
                CreateNewLaracombeeClass::class,
            ]);
        }
    }
}
