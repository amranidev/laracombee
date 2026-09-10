<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;

/**
 * Synchronize configured Eloquent records in bounded database batches.
 */
class SeedCommand extends LaracombeeCommand
{
    /**
     * The Artisan command name, arguments, and options.
     *
     * @var string
     */
    protected $signature = 'laracombee:seed {type : Catalog type (user or item)} {--chunk=100 : Records per batch}';

    /**
     * The command description displayed in Artisan help.
     *
     * @var string
     */
    protected $description = 'Seed records into Recombee';

    /**
     * Execute the command and return its success or failure exit code.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->executeOperation(function () {
            $chunk = filter_var($this->option('chunk'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($chunk === false) {
                throw new \InvalidArgumentException('Chunk size must be a positive integer.');
            }

            $type = $this->catalogType($this->argument('type'));
            $class = $this->modelClass($type);
            $this->modelProperties($type);
            $bar = $this->output->createProgressBar($class::query()->count());

            $class::query()->chunkById($chunk, function ($records) use ($type, $bar) {
                $batch = $this->{'add'.ucfirst($type).'s'}($records->all());
                Laracombee::batch($batch)->wait();
                $bar->advance($records->count());
            });

            $bar->finish();
            $this->newLine();
        });
    }
}
