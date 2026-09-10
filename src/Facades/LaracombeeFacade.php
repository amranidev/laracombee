<?php

namespace Amranidev\Laracombee\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Expose the configured package client through Laravel’s facade mechanism.
 *
 * @mixin \Amranidev\Laracombee\Laracombee
 */
class LaracombeeFacade extends Facade
{
    /**
     * Identify the container binding resolved by this facade.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laracombee';
    }
}
