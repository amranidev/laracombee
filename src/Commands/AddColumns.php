<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Laracombee;

class AddColumns extends Command
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
            list($property, $type) = explode(':', $column);

            return $this->{'add'.ucfirst($this->option('to')).'Property'}($property, $type);
        });
    }

    /**
     * Add User property.
     *
     * @param string $property.
     * @param string $type.
     *
     * @return \Recombee\RecommApi\Requests\AddUserProperty
     */
    public function addUserProperty(string $property, string $type)
    {
        return Laracombee::addUserProperty($property, $type);
    }

    /**
     * Add Item property.
     *
     * @param string $property.
     * @param string $type.
     *
     * @return \Recombee\RecommApi\Requests\AddItemProperty
     */
    public function addItemProperty(string $property, string $type)
    {
        return Laracombee::addItemProperty($property, $type);
    }
}
