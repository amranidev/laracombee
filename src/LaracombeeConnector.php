<?php

namespace Amranidev\Laracombee;

class LaracombeeConnector
{
    /**
     * @return Laracombee
     */
    public function connect(): Laracombee
    {
        return new Laracombee();
    }
}
