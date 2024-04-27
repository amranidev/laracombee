<?php

namespace Amranidev\Laracombee\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageAliases($app)
    {
        return [
            'Laracombee' => 'Amranidev\Laracombee\Facades\LaracombeeFacade',
        ];
    }

    protected function getPackageProviders($app)
    {
        return ['Amranidev\Laracombee\Providers\LaracombeeServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laracombee.database', 'amranidev-laracombee');
        $app['config']->set('laracombee.token', 'In8pEgDs0PbgdT1QOZupTRP0HP5eMWWdgvjdq6WjmwRUNjhfg2556na0vHQyy88J');
        $app['config']->set('laracombee.timeout', '5000');
        $app['config']->set('laracombee.protocol', 'https');
    }
}
