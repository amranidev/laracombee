<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\Laracombee\Tests\Fakes\FakeLaracombee;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('laracombee.database', 'amranidev-laracombee');
        $app['config']->set('laracombee.token', 'test-token');
        $app['config']->set('laracombee.timeout', 5000);
        $app['config']->set('laracombee.protocol', 'https');
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Laracombee' => 'Amranidev\Laracombee\Facades\LaracombeeFacade',
        ];
    }

    protected function getPackageProviders($app): array
    {
        return ['Amranidev\Laracombee\Providers\LaracombeeServiceProvider'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton('laracombee', static fn () => new FakeLaracombee());
    }
}
