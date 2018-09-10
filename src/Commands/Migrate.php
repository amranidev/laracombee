<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Recombee\RecommApi\Client;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:migrate
    						{type : Catalog type (User or Item)}
    						{--class= : Laravel model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate to recombee';

    /**
     * @var \Recombee\RecommApi\Client
     */
    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new Client(config('laracombee.database'), config('laracombee.token'));
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

        $scope = $this->prepareScope();
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
                $this->info("Nothing to migrate");
                die();
        }
    }

    /**
     * Prepare User Properties.
     */
    public function prepareUserProperties()
    {
    }
}
