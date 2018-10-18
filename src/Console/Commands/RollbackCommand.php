<?php

namespace Amranidev\Laracombee\Console\Commands;

use Laracombee;
use Amranidev\Laracombee\Console\LaracombeeCommand;

class RollbackCommand extends LaracombeeCommand
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
        if (!$this->option('class')) {
            $this->error('--class option is required!');
            die();
        }

        $class = $this->option('class');
        $properties = $class::$laracombee;

        return collect($properties)->map(function (string $type, string $property) {
            return $this->{'delete'.ucfirst($this->argument('type')).'Property'}($property, $type);
        });
    }
}
