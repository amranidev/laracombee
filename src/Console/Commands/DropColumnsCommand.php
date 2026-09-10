<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;
use Illuminate\Support\Collection;

/**
 * Remove explicitly supplied properties from a Recombee catalog.
 */
class DropColumnsCommand extends LaracombeeCommand
{
    /**
     * The Artisan command name, arguments, and options.
     *
     * @var string
     */
    protected $signature = 'laracombee:drop {columns* : Properties} {--from= : Catalog type (user or item)}';

    /**
     * The command description displayed in Artisan help.
     *
     * @var string
     */
    protected $description = 'Drop Recombee properties';

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
        $type = $this->catalogType($this->option('from'));

        return collect($columns)->map(function (string $column) use ($type) {
            if ($column === '') {
                throw new \InvalidArgumentException('Property names must not be empty.');
            }

            return $this->{'delete'.ucfirst($type).'Property'}($column);
        });
    }
}
