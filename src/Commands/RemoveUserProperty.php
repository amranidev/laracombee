<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\DeleteUserProperty;

class RemoveUserProperty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:remove-user-props
                            {property : Property name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Recombee User Property';

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
     * @return mixed
     */
    public function handle()
    {
        $this->client->send(new DeleteUserProperty($this->argument('property')));
    }
}
