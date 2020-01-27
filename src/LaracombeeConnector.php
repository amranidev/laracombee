<?php

namespace Amranidev\Laracombee;

use Illuminate\Queue\Connectors\ConnectorInterface;

class LaracombeeConnector
{
    /**
     * @param array $config
     * @return Laracombee
     */
    public function connect()
    {
        return new Laracombee();
    }
}
