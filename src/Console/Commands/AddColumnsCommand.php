<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;
use Illuminate\Support\Collection;

/**
 * Create explicitly supplied properties in a Recombee catalog.
 */
class AddColumnsCommand extends LaracombeeCommand
{
    /**
     * The Artisan command name, arguments, and options.
     *
     * @var string
     */
    protected $signature = 'laracombee:add {columns* : Properties} {--to= : Catalog type (user or item)}';

    /**
     * The command description displayed in Artisan help.
     *
     * @var string
     */
    protected $description = 'Add Recombee properties';

    /**
     * Execute the command and return its success or failure exit code.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->executeOperation(function () {
            Laracombee::batch($this->loadColumns($this->argument('columns'))->all())->wait();
        });
    }

    /**
     * Validate property arguments and build their Recombee requests.
     *
     * @param array<int, string> $columns
     * @return \Illuminate\Support\Collection
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    public function loadColumns(array $columns): Collection
    {
        $type = $this->catalogType($this->option('to'));

        return collect($columns)->map(function (string $column) use ($type) {
            $parts = explode(':', $column);
            if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                throw new \InvalidArgumentException('Columns must use the name:type format.');
            }

            return $this->{'add'.ucfirst($type).'Property'}($parts[0], $parts[1]);
        });
    }
}
