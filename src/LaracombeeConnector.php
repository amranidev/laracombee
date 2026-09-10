<?php

namespace Amranidev\Laracombee;

use Recombee\RecommApi\Client;

/**
 * Create independently configured Recombee clients with optional model mapping.
 */
class LaracombeeConnector
{
    /**
     * Create a client for the supplied configuration or the Laravel package defaults.
     *
     * @param array<string, mixed>|null $configuration
     * @param ModelMapper|null $mapper
     * @return Laracombee
     */
    public function connect(?array $configuration = null, ?ModelMapper $mapper = null): Laracombee
    {
        $configuration ??= config('laracombee');
        $client = new Client($configuration['database'] ?? '', $configuration['token'] ?? '', [
            'timeout' => $configuration['timeout'] ?? 2000,
            'region' => $configuration['region'] ?? 'eu-west',
            'protocol' => $configuration['protocol'] ?? 'https',
        ]);

        return new Laracombee($client, $mapper, $configuration);
    }
}
