<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Facades\LaracombeeFacade;
use Illuminate\Console\Command;
use Amranidev\Laracombee\Console\LaracombeeCommand;

class SeedCommand extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:seed
                            {type : Catalog type (user or item)}
                            {--chunk= : total chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed records into recombee db';

    /**
     * The default user model.
     *
     * @var string
     */
    protected static $userModel = '\\App\\User';

    /**
     * The default chunk value.
     *
     * @var int
     */
    protected static $chunk = 100;

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
        $chunk = (int) $this->option('chunk') ?: self::$chunk;

        $class = config('laracombee.'.$this->argument('type'));

        $records = $class::all();

        $total = $records->count();

        $bar = $this->output->createProgressBar($total / $chunk);

        $records->chunk($chunk)->each(function ($users) use ($bar) {
            $batch = $this->{'add'.ucfirst($this->argument('type')).'s'}($users->all());
            LaracombeeFacade::batch($batch)->then(function ($response) {
            })->otherwise(function ($error) {
                $this->info('');
                $this->error($error);
                exit;
            })->wait();

            $bar->advance();
        });

        $bar->finish();

        $this->info('');
        $this->info('Done!');
    }
}