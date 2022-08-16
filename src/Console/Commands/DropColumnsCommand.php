<?php

namespace Amranidev\Laracombee\Console\Commands;

use Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;

class DropColumnsCommand extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:drop
                            {columns* : Columns}
                            {--from= : table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop columns form recombee db';

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
        if (!$this->option('from')) {
            $this->error('--from option is required!');
            exit();
        }

        Laracombee::batch($this->loadColumns($this->argument('columns'))->all())
            ->then(function ($response) {
                $this->info('Done!');
            })
            ->otherwise(function ($error) {
                $this->error($error);
            })
            ->wait();
    }

    /**
     * Load columns.
     *
     * @param array $columns
     *
     * @return \Illuminate\Support\Collection
     */
    public function loadColumns(array $columns)
    {
        return collect($columns)->map(function (string $column) {
            return $this->{'delete'.ucfirst($this->option('from')).'Property'}($column);
        });
    }
}
