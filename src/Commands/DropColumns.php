<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Laracombee;

class DropColumns extends Command
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
            die();
        }

        Laracombee::batch($this->loadColumns($this->argument('columns'))->all());

        $this->info('Done!');
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
            return $this->{'delete' . ucfirst($this->option('from')) . 'Property'}($column);
        });
    }

    /**
     * Delete User property.
     *
     * @param string $property.
     *
     * @return \Recombee\RecommApi\Requests\DeleteUserProperty
     */
    public function deleteUserProperty(string $property)
    {
        return Laracombee::deleteUserProperty($property);
    }

    /**
     * Delete Item property.
     *
     * @param string $property.
     *
     * @return \Recombee\RecommApi\Requests\DeleteItemProperty
     */
    public function deleteItemProperty(string $property)
    {
        return Laracombee::deleteItemProperty($property);
    }
}
