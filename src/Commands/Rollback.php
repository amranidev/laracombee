<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Laracombee;

class Rollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:rollback
    						{type : Catalog type (user or item)}
    						{--class= : Laravel model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate to recombee';

    /**
     * The default user model.
     *
     * @var string
     */
    protected static $userModel = '\\App\\User';

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
        $scope = $this->prepareScope();

        Laracombee::batch($scope);

        $this->info('Done!');
    }

    /**
     * Prepare scope.
     *
     * @return mixed
     */
    public function prepareScope()
    {
        switch ($this->argument('type')) {
            case 'user':
                return $this->prepareUserProperties();
            case 'item':
                return $this->prepareItemProperties();
            default:
                $this->info('Nothing to migrate');
                die();
        }
    }

    /**
     * Prepare User Properties.
     *
     * @return \Illuminate\Support\Collection
     */
    public function prepareUserProperties()
    {
        $class = $this->option('class') ?: self::$userModel;
        $properties = $class::$laracombee;

        return collect($properties)->map(function ($type, $property) {
            return Laracombee::deleteUserProperty($property);
        });
    }

    /**
     * Prepare Item Properties.
     *
     * @return Illuminate\Support\Collection
     */
    public function prepareItemProperties()
    {
        if (!$this->option('class')) {
            $this->error('--class option is required!');
            die();
        }

        $class = $this->option('class');
        $properties = $class::$laracombee;

        return collect($properties)->map(function ($type, $property) {
            return Laracombee::deleteItemProperty($property);
        });
    }
}
