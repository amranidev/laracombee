<?php

namespace Amranidev\Laracombee\Commands;

use Amranidev\Laracombee\Commands\LaracombeeCommand;
use Laracombee;

class Migrate extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:migrate
    						{type : Catalog type (user or item)}
    						{--class= : Laravel model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate to recombee';

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
     * @return void
     */
    public function handle()
    {
        if (!$this->option('class')) {
            $this->error('--class option is required!');
            die();
        }

        $scope = $this->prepareScope()->all();

        Laracombee::batch($scope)
            ->then(function ($response) {
                $this->info('Done!');
            })
            ->otherwise(function ($error) {
                $this->error($error);
            })
            ->wait();
    }

    /**
     * Prepare scope.
     *
     * @return mixed
     */
    public function prepareScope()
    {
        $class = $this->option('class');
        $properties = $class::$laracombee;

        return collect($properties)->map(function (string $type, string $property) {
            return $this->{'add' . ucfirst($this->argument('type')) . 'Property'}($property, $type);
        });
    }
}
