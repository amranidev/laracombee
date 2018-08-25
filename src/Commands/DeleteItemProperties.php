<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\DeleteItemProperty;

class DeleteItemProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:rm-item-props
                            {properties* : Properties}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Recombee Item Property';

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
        $properties = collect($this->argument('properties'));

        $bar = $this->output->createProgressBar($properties->count());

        $properties->each(function ($property) use ($bar) {
            $this->client->send(new DeleteItemProperty($property));
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info("Delelted Successfully!");
    }
}