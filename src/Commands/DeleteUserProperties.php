<?php

namespace Amranidev\Laracombee\Commands;

use Illuminate\Console\Command;
use Laracombee;

class DeleteUserProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:rm-user-props
                            {properties* : Properties}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Recombee User Property';

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
        $properties = collect($this->argument('properties'));

        $bar = $this->output->createProgressBar($properties->count());

        $properties->each(function ($property) use ($bar) {
            Laracombee::send(Laracombee::deleteUserProperty($property));
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info('Delelted Successfully!');
    }
}
