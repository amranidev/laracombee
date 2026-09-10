<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;

/**
 * Erase Recombee data only after interactive confirmation.
 */
class ResetDatabaseCommand extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset recombee database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (!$this->confirm('This permanently erases all Recombee data. Are you sure?')) {
            return self::SUCCESS;
        }

        return $this->executeOperation(function () {
            Laracombee::send(Laracombee::resetDatabase())->wait();
            $this->info('Recombee data has been erased!');
        });
    }
}
