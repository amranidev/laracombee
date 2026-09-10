<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\Laracombee\Laracombee;
use Amranidev\Laracombee\Providers\LaracombeeServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;

/**
 * Verify package bootstrapping, container bindings, and mapper customization.
 */
class ServiceProviderTest extends TestCase
{
    /**
     * Register the package provider in the Testbench application.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [LaracombeeServiceProvider::class];
    }

    /**
     * Configure an isolated Laravel environment for this test case.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('laracombee.database', 'test-database');
        $app['config']->set('laracombee.token', 'test-token');
    }

    /**
     * Verify it resolves the real client with merged configuration.
     *
     * @return void
     */
    public function testItResolvesTheRealClientWithMergedConfiguration(): void
    {
        $client = $this->app->make('laracombee');

        $this->assertInstanceOf(Laracombee::class, $client);
        $this->assertSame($client, $this->app->make('laracombee'));
        $this->assertSame($client, $this->app->make(Laracombee::class));
        $this->assertSame('test-database', config('laracombee.database'));
        $this->assertSame(2000, config('laracombee.timeout'));
        $this->assertSame('eu-west', config('laracombee.region'));
    }

    /**
     * Verify it registers artisan commands.
     *
     * @return void
     */
    public function testItRegistersArtisanCommands(): void
    {
        $commands = Artisan::all();

        foreach (['seed', 'migrate', 'rollback', 'add', 'drop', 'reset', 'new'] as $command) {
            $this->assertArrayHasKey('laracombee:'.$command, $commands);
        }

        $this->artisan('help', ['command_name' => 'laracombee:migrate'])->assertExitCode(0);
    }
    /**
     * Verify configured mapper is injected into the client.
     *
     * @return void
     */
    public function testConfiguredMapperIsInjectedIntoTheClient(): void
    {
        $mapper = new class extends \Amranidev\Laracombee\ModelMapper {
            /**
             * Supply deterministic mapped data for this test fixture.
             *
             * @param object $model
             * @return string|int
             */
            public function identifier(object $model): string|int
            {
                return 'custom-id';
            }

            /**
             * Supply deterministic mapped data for this test fixture.
             *
             * @param object $model
             * @return array<string, mixed>
             */
            public function values(object $model): array
            {
                return ['name' => 'Custom'];
            }
        };
        $this->app->instance(\Amranidev\Laracombee\ModelMapper::class, $mapper);
        $client = $this->app->make(Laracombee::class);
        $request = $client->addItem(new class extends \Illuminate\Database\Eloquent\Model {});

        $this->assertSame('/{databaseId}/items/custom-id', $request->getPath());
        $this->assertSame('Custom', $request->getBodyParameters()['name']);
    }
}
