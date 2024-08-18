<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade;
use Amranidev\Laracombee\Console\LaracombeeCommand;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('All your recombee data will be erased, including items, item properties, series, user database, purchases, ratings, detail views, and bookmarks. Make sure the request to be never executed in production environment! Resetting your database is irreversible. Are you sure?')) {
            $request = LaracombeeFacade::resetDatabase();
            LaracombeeFacade::send($request)->then(function ($response) {
                $this->info('Recombee data has been erased!');
            })->otherwise(function ($error) {
                $this->error($error);
            })->wait();
        }
    }
}