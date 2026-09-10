<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\Laracombee\Laracombee;
use Amranidev\Laracombee\ModelMapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use PHPUnit\Framework\TestCase;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\Request;
use RuntimeException;

/**
 * Verify SDK execution, exception propagation, and model mapping without network calls.
 */
class ClientTest extends TestCase
{
    /**
     * Verify send is deferred and passes the configured timeout.
     *
     * @return void
     */
    public function testSendIsDeferredAndPassesTheConfiguredTimeout(): void
    {
        $sdk = $this->createMock(Client::class);
        $client = new Laracombee($sdk, null, ['timeout' => 1234]);
        $request = $client->deleteItem('item-1');
        $called = false;
        $sdk->expects($this->once())->method('send')->with($request)->willReturnCallback(
            function (Request $actual) use (&$called) {
                $called = true;
                $this->assertSame(1234, $actual->getTimeout());

                return 'ok';
            }
        );

        $promise = $client->send($request);
        $this->assertFalse($called);
        $this->assertSame('ok', $promise->wait());
        $this->assertTrue($called);
    }

    /**
     * Verify send preserves the original exception.
     *
     * @return void
     */
    public function testSendPreservesTheOriginalException(): void
    {
        $sdk = $this->createMock(Client::class);
        $failure = new RuntimeException('SDK failed');
        $sdk->expects($this->once())->method('send')->willThrowException($failure);
        $client = new Laracombee($sdk, null, []);

        try {
            $client->send($client->deleteItem('item-1'))->wait();
            $this->fail('Expected the SDK exception.');
        } catch (RuntimeException $actual) {
            $this->assertSame($failure, $actual);
        }
    }

    /**
     * Verify items use custom keys and only export declared visible properties.
     *
     * @return void
     */
    public function testItemsUseCustomKeysAndOnlyExportDeclaredVisibleProperties(): void
    {
        $client = new Laracombee($this->createStub(Client::class), null, []);
        $item = new class extends Model {
            /**
             * The custom primary key used by this model fixture.
             *
             * @var string
             */
            protected $primaryKey = 'sku';
            /**
             * The model fixture’s primary-key storage type.
             *
             * @var string
             */
            protected $keyType = 'string';
            /**
             * Whether the model fixture uses an auto-incrementing key.
             *
             * @var bool
             */
            public $incrementing = false;
            /**
             * Attributes excluded from mass assignment in the fixture.
             *
             * @var array<int, string>
             */
            protected $guarded = [];
            /**
             * Attributes excluded from serialized fixture values.
             *
             * @var array<int, string>
             */
            protected $hidden = ['secret'];
            /**
             * The Recombee schema exported by this model fixture.
             *
             * @var array<string, string>
             */
            public static $laracombee = ['name' => 'string', 'secret' => 'string'];
        };
        $item->forceFill(['sku' => 'sku-42', 'name' => 'Book', 'secret' => 'hidden', 'other' => 'ignored']);
        $request = $client->addItem($item);

        $this->assertSame('/{databaseId}/items/sku-42', $request->getPath());
        $this->assertSame(['name' => 'Book', '!cascadeCreate' => true], $request->getBodyParameters());
    }

    /**
     * Verify merge users uses custom keys and the sdk cascade option.
     *
     * @return void
     */
    public function testMergeUsersUsesCustomKeysAndTheSdkCascadeOption(): void
    {
        $client = new Laracombee($this->createStub(Client::class), null, []);
        $target = new class extends User {
            /**
             * The custom primary key used by this model fixture.
             *
             * @var string
             */
            protected $primaryKey = 'user_key';
            /**
             * The model fixture’s primary-key storage type.
             *
             * @var string
             */
            protected $keyType = 'string';
            /**
             * Whether the model fixture uses an auto-incrementing key.
             *
             * @var bool
             */
            public $incrementing = false;
        };
        $source = clone $target;
        $target->setAttribute('user_key', 'target');
        $source->setAttribute('user_key', 'source');
        $request = $client->mergeUsers($target, $source);

        $this->assertStringContainsString('target', $request->getPath());
        $this->assertTrue($request->getQueryParameters()['cascadeCreate']);
    }

    /**
     * Verify missing model identifiers produce an actionable error.
     *
     * @return void
     */
    public function testMissingModelIdentifiersProduceAnActionableError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('identifier');
        (new ModelMapper())->identifier(new class extends Model {});
    }

    /**
     * Verify missing property mapping produces an actionable error.
     *
     * @return void
     */
    public function testMissingPropertyMappingProducesAnActionableError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$laracombee');
        (new ModelMapper())->values(new class extends Model {});
    }

    /**
     * Verify custom mapper can override exported values.
     *
     * @return void
     */
    public function testCustomMapperCanOverrideExportedValues(): void
    {
        $mapper = new class extends ModelMapper {
            /**
             * Supply deterministic mapped data for this test fixture.
             *
             * @param object $model
             * @return string|int
             */
            public function identifier(object $model): string|int { return 'external-id'; }
            /**
             * Supply deterministic mapped data for this test fixture.
             *
             * @param object $model
             * @return array<string, mixed>
             */
            public function values(object $model): array { return ['name' => 'Mapped']; }
        };
        $client = new Laracombee($this->createStub(Client::class), $mapper, []);
        $request = $client->addItem(new class extends Model {});
        $this->assertSame('/{databaseId}/items/external-id', $request->getPath());
        $this->assertSame('Mapped', $request->getBodyParameters()['name']);
    }
}
