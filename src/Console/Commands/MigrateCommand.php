<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;
use Illuminate\Support\Collection;

/**
 * Create Recombee properties declared by the configured model.
 */
class MigrateCommand extends LaracombeeCommand
{
    /**
     * The Artisan command name, arguments, and options.
     *
     * @var string
     */
    protected $signature = 'laracombee:migrate {type : Catalog type (user or item)}';

    /**
     * The command description displayed in Artisan help.
     *
     * @var string
     */
    protected $description = 'Create Recombee properties';

    /**
     * Execute the command and return its success or failure exit code.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->executeOperation(function () {
            $requests = $this->prepareScope()->all();
            if ($requests !== []) {
                Laracombee::batch($requests)->wait();
            }
        });
    }

    /**
     * Build schema requests from the configured model’s property definitions.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    public function prepareScope(): Collection
    {
        $type = $this->catalogType($this->argument('type'));

        return collect($this->modelProperties($type))->map(function (string $propertyType, string $property) use ($type) {
            return $this->{'add'.ucfirst($type).'Property'}($property, $propertyType);
        });
    }
}
