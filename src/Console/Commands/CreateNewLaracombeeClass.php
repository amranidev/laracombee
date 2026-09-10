<?php

namespace Amranidev\Laracombee\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Generate a custom client subclass without overwriting existing files.
 */
class CreateNewLaracombeeClass extends GeneratorCommand
{
    /**
     * The Artisan command name, arguments, and options.
     *
     * @var string
     */
    protected $signature = 'laracombee:new {name : Class name}';

    /**
     * The command description displayed in Artisan help.
     *
     * @var string
     */
    protected $description = 'Create a custom Laracombee client';

    /**
     * The generated artifact name shown in command output.
     *
     * @var string
     */
    protected $type = 'Laracombee client';

    /**
     * Execute the command and return its success or failure exit code.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->getNameInput();
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)*$/D', $name)) {
            $this->error('Use a valid PHP class name, optionally with a namespace.');

            return self::FAILURE;
        }

        try {
            return parent::handle() === false ? self::FAILURE : self::SUCCESS;
        } catch (\Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Locate the template used to generate a custom client class.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../resources/stubs/laracombee-class.stub';
    }

    /**
     * Place generated clients in the application’s Laracombee namespace.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Laracombee';
    }
}
