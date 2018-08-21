<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\AddUserProperty;

class DefineUserProperties extends Command 
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'laracombee:user-props';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Eecombee User Properties';

    /**
     * @var \Recombee\RecommApi\Client
     */
    protected $client;

    /**
     * @var Collection
     */
    protected $properties;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new Client(config('laracombee.database'), config('laracombee.token'));
        $this->properties = $this->loadProperties();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar($this->properties->count());

        $client = $this->client;

        $this->properties->each(function ($property) use ($client, $bar) {
            $client->send($property);
            $bar->advance();
        });

        $bar->finish();
    }

    private function loadProperties()
    {
        return collect(config('laracombee.user-properties'))->map(function ($value, $property) {
            return new AddUserProperty($property, $value);
        });
    }
}
