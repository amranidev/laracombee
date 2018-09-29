<?php

namespace Amranidev\Laracombee\Console\Commands;

use Amranidev\Laracombee\Console\LaracombeeCommand;
use Illuminate\Console\Command;
use Laracombee;

class SeedCommand extends LaracombeeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:seed
                            {type : Catalog type (user or item)}
                            {--class= : Laravel model}
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
        if (!$this->option('class')) {
            $this->error('--class option is required!');
            die();
        }

        $chunk = (int) $this->option('chunk') ?: self::$chunk;

        $class = $this->option('class');

        $records = $class::all();

        $total = $records->count();

        $bar = $this->output->createProgressBar($total / $chunk);

        $records->chunk($chunk)->each(function ($users) use ($bar) {
            $batch = $this->{'add'.ucfirst($this->argument('type')).'s'}($users->all());
            Laracombee::batch($batch)->then(function ($response) use ($bar) {
            })->otherwise(function ($error) {
                $this->info();
                $this->error($error);
                die();
            })->wait();

            $bar->advance();
        });

        $bar->finish();

        $this->info('');
        $this->info('Done!');
    }
}
