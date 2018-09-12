<?php

namespace Amranidev\Laracombee\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageAliases($app)
    {
        return [
            'Laracombee' => 'Amranidev\Laracombee\Facades\LaracombeeFacade'
        ];
    }
    protected function getPackageProviders($app)
    {
        return ['Amranidev\Laracombee\Providers\LaracombeeServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laracombee.database', 'amranidev');
        $app['config']->set('laracombee.token', 'Ziyu6NtLU7Be9O5AhdwFNvasnSOZj35b0vbJbmQLexnu5xtQEtV01bv8Xpa68Hzi');
    }
}
