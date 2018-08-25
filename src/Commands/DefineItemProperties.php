<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\AddItemProperty;

class DefineItemProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:add-item-props';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Recombee Item Properties';

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
     * @return void
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar($this->properties->count());

        $this->properties->each(function ($property) use ($bar) {
            $this->client->send($property);
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info('Created Successfully');
    }

    /**
     * Load Peroperties.
     *
     * @return \Illuminate\Support\Collection
     */
    private function loadProperties()
    {
        return collect(config('laracombee.item-properties'))->map(function ($type, $property) {
            return new AddItemProperty($property, $type);
        });
    }
}