<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\Laracombee\Tests\Fakes\FakeLaracombee;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Boot the package with deterministic configuration and a fake API client.
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Configure an isolated Laravel environment for this test case.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('laracombee.database', 'amranidev-laracombee');
        $app['config']->set('laracombee.token', 'test-token');
        $app['config']->set('laracombee.timeout', 5000);
        $app['config']->set('laracombee.protocol', 'https');
    }

    /**
     * Register the facade alias used by the package API tests.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Laracombee' => 'Amranidev\Laracombee\Facades\LaracombeeFacade',
        ];
    }

    /**
     * Register the package provider in the Testbench application.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return ['Amranidev\Laracombee\Providers\LaracombeeServiceProvider'];
    }

    /**
     * Prepare the application and fixtures before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton('laracombee', static fn () => new FakeLaracombee());
    }
}
