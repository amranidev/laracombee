<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\Laracombee\Laracombee;
use Amranidev\Laracombee\Providers\LaracombeeServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\Request;

/**
 * Exercise Artisan validation, SDK failures, database batching, and client generation.
 */
class CommandsTest extends TestCase
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
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        $app['config']->set('laracombee.item', CatalogItem::class);
    }

    /**
     * Bind a real package client backed by a mock SDK for command assertions.
     *
     * @return \Recombee\RecommApi\Client&\PHPUnit\Framework\MockObject\MockObject
     */
    private function sdk(): Client
    {
        $sdk = $this->createMock(Client::class);
        $this->app->instance('laracombee', new Laracombee($sdk, null, []));

        return $sdk;
    }

    /**
     * Verify property commands execute without aglobal facade alias.
     *
     * @return void
     */
    public function testPropertyCommandsExecuteWithoutAGlobalFacadeAlias(): void
    {
        $this->sdk()->expects($this->exactly(4))->method('send')->with($this->isInstanceOf(Batch::class))->willReturn('ok');
        $this->artisan('laracombee:add', ['columns' => ['name:string'], '--to' => 'item'])->assertExitCode(0);
        $this->artisan('laracombee:drop', ['columns' => ['name'], '--from' => 'item'])->assertExitCode(0);
        $this->artisan('laracombee:migrate', ['type' => 'item'])->assertExitCode(0);
        $this->artisan('laracombee:rollback', ['type' => 'item'])->assertExitCode(0);
    }

    /**
     * Verify invalid arguments fail before sending requests.
     *
     * @return void
     */
    public function testInvalidArgumentsFailBeforeSendingRequests(): void
    {
        $this->sdk()->expects($this->never())->method('send');
        $this->artisan('laracombee:add', ['columns' => ['name:string']])->assertExitCode(1);
        $this->artisan('laracombee:add', ['columns' => ['malformed'], '--to' => 'item'])->assertExitCode(1);
        $this->artisan('laracombee:drop', ['columns' => ['name'], '--from' => 'unknown'])->assertExitCode(1);
        $this->artisan('laracombee:migrate', ['type' => 'unknown'])->assertExitCode(1);
        $this->artisan('laracombee:rollback', ['type' => 'user'])->assertExitCode(1);
        foreach (['0', '-1', 'abc', '1.5'] as $chunk) {
            $this->artisan('laracombee:seed', ['type' => 'item', '--chunk' => $chunk])->assertExitCode(1);
        }
    }

    /**
     * Verify sdk failure returns anonzero exit code.
     *
     * @return void
     */
    public function testSdkFailureReturnsANonzeroExitCode(): void
    {
        $this->sdk()->expects($this->once())->method('send')->willThrowException(new \RuntimeException('Service unavailable'));
        $this->artisan('laracombee:add', ['columns' => ['name:string'], '--to' => 'item'])
            ->expectsOutput('Service unavailable')->assertExitCode(1);
    }

    /**
     * Verify seeding reads bounded database chunks.
     *
     * @return void
     */
    public function testSeedingReadsBoundedDatabaseChunks(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->increments('catalog_id');
            $table->string('name');
        });
        foreach (range(1, 5) as $id) {
            CatalogItem::query()->create(['name' => 'Item '.$id]);
        }
        $this->sdk()->expects($this->exactly(3))->method('send')->with($this->isInstanceOf(Batch::class))->willReturn('ok');
        DB::enableQueryLog();
        $this->artisan('laracombee:seed', ['type' => 'item', '--chunk' => '2'])->assertExitCode(0);
        $queries = array_column(DB::getQueryLog(), 'query');
        $reads = array_values(array_filter($queries, static fn ($sql) => str_contains($sql, 'select *')));
        $this->assertCount(3, $reads);
        foreach ($reads as $sql) {
            $this->assertStringContainsString('limit 2', strtolower($sql));
        }
    }

    /**
     * Verify declining reset does not send arequest.
     *
     * @return void
     */
    public function testDecliningResetDoesNotSendARequest(): void
    {
        $this->sdk()->expects($this->never())->method('send');
        $this->artisan('laracombee:reset')
            ->expectsConfirmation('This permanently erases all Recombee data. Are you sure?', 'no')
            ->assertExitCode(0);
    }

    /**
     * Verify confirmed reset sends arequest.
     *
     * @return void
     */
    public function testConfirmedResetSendsARequest(): void
    {
        $this->sdk()->expects($this->once())->method('send')->with($this->isInstanceOf(Request::class))->willReturn('ok');
        $this->artisan('laracombee:reset')
            ->expectsConfirmation('This permanently erases all Recombee data. Are you sure?', 'yes')
            ->assertExitCode(0);
    }

    /**
     * Verify generator creates ausable class and refuses to overwrite it.
     *
     * @return void
     */
    public function testGeneratorCreatesAUsableClassAndRefusesToOverwriteIt(): void
    {
        $directory = sys_get_temp_dir().'/laracombee-generator-'.bin2hex(random_bytes(6));
        mkdir($directory, 0755, true);
        $originalPath = $this->app->path();
        $namespace = $this->app->getNamespace();
        $this->app->useAppPath($directory);
        $class = 'GeneratedClient'.bin2hex(random_bytes(6));
        $path = $directory.'/Laracombee/'.$class.'.php';

        try {
            $status = \Illuminate\Support\Facades\Artisan::call('laracombee:new', ['name' => $class]);
            $this->assertSame(0, $status, \Illuminate\Support\Facades\Artisan::output());
            $this->assertFileExists($path);
            require $path;
            $qualified = $namespace.'Laracombee\\'.$class;
            $this->assertInstanceOf(Laracombee::class, new $qualified($this->createStub(Client::class), null, []));
            $original = file_get_contents($path);
            $this->artisan('laracombee:new', ['name' => $class])->assertExitCode(1);
            $this->assertSame($original, file_get_contents($path));
            $this->artisan('laracombee:new', ['name' => '../Invalid'])->assertExitCode(1);
            $this->artisan('laracombee:new', ['name' => 'class'])->assertExitCode(1);
        } finally {
            $this->app->useAppPath($originalPath);
            (new \Illuminate\Filesystem\Filesystem())->deleteDirectory($directory);
        }
    }
}

/**
 * Provide an Eloquent catalog fixture with a custom primary key.
 */
class CatalogItem extends Model
{
    /**
     * The database table used by this model fixture.
     *
     * @var string
     */
    protected $table = 'catalog_items';
    /**
     * The custom primary key used by this model fixture.
     *
     * @var string
     */
    protected $primaryKey = 'catalog_id';
    /**
     * Attributes excluded from mass assignment in the fixture.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    /**
     * Whether Eloquent maintains timestamps for the fixture.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The Recombee schema exported by this model fixture.
     *
     * @var array<string, string>
     */
    public static $laracombee = ['name' => 'string'];
}
