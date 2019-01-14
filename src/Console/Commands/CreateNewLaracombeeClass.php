<?php

namespace Amranidev\Laracombee\Console\Commands;

use Illuminate\Console\Command;

class CreateNewLaracombeeClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracombee:new
                            {name : Class Name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create laracombee class';

    /**
     * Class path.
     *
     * @var string
     */
    private $path = 'app/Laracombee/';

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
        $className = ucfirst($this->argument('name'));
        if (!is_dir($destination = base_path($this->path))) {
            mkdir($destination, 077, true);
        }
        $template = file_get_contents(__DIR__.'/../../../resources/stubs/laracombee-class.stub');
        $content = str_replace('__CLASSNAME__', $className, $template);
        file_put_contents($this->path.$className.'.php', $content, '', '', '');
    }
}
