<?php

namespace Amranidev\Laracombee;

class LaracombeeConnector
{
    /**
     * @param array $config
     *
     * @return Laracombee
     */
    public function connect()
    {
        return new Laracombee();
    }
}
