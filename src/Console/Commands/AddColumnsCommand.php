<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Console\LaracombeeCommand;
use Laracombee;

class AddColumnsCommand extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:add
                            {columns* : Columns}
                            {--to= : table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new columns to recombee db';

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
        if (!$this->option('to')) {
            $this->error('--to option is required!');
            die();
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
            list($property, $type) = explode(':', $column);

            return $this->{'add'.ucfirst($this->option('to')).'Property'}($property, $type);
        });
    }
}
