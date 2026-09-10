<?php

namespace Amranidev\Laracombee;

use GuzzleHttp\Promise\Promise;
use Recombee\RecommApi\Client;
use Throwable;
use Illuminate\Database\Eloquent\Model;
use Recombee\RecommApi\Requests\Request;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Map Laravel models to Recombee requests and execute deferred SDK calls.
 */
class Laracombee extends AbstractRecombee
{
    /**
     * The strategy used to map model identifiers and properties.
     *
     * @var ModelMapper
     */
    protected ModelMapper $mapper;

    /**
     * Initialize the client dependencies and configuration.
     *
     * @param \Recombee\RecommApi\Client|null $client
     * @param ModelMapper|null $mapper
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(?Client $client = null, ?ModelMapper $mapper = null, ?array $configuration = null)
    {
        $configuration ??= config('laracombee');
        $this->mapper = $mapper ?? new ModelMapper();

        parent::__construct(
            $configuration['database'] ?? '',
            $configuration['token'] ?? '',
            [
                'timeout' => $configuration['timeout'] ?? 2000,
                'region' => $configuration['region'] ?? 'eu-west',
                'protocol' => $configuration['protocol'] ?? 'https',
            ],
            $client
        );
    }

    /**
     * Build Recombee value-setting requests from the supplied model.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Recombee\RecommApi\Requests\SetUserValues
     */
    public function addUser(Authenticatable $user): \Recombee\RecommApi\Requests\SetUserValues
    {
        return $this->setUserValues($this->mapper->identifier($user), $this->mapper->values($user));
    }

    /**
     * Build Recombee value-setting requests from the supplied models.
     *
     * @param array<\Illuminate\Contracts\Auth\Authenticatable> $users
     * @return array<\Recombee\RecommApi\Requests\SetUserValues>
     */
    public function addUsers(array $users): array
    {
        return array_map(function ($user) {
            return $this->addUser($user);
        }, $users);
    }

    /**
     * Update a user in recombee.
     *
     * @return \Recombee\RecommApi\Requests\SetUserValues
     */
    public function updateUser(Authenticatable $user): \Recombee\RecommApi\Requests\SetUserValues
    {
        return $this->addUser($user);
    }

    /**
     * Merge users.
     *
     * @return \Recombee\RecommApi\Requests\MergeUsers
     */
    public function mergeUsers(Authenticatable $target_user, Authenticatable $source_user): \Recombee\RecommApi\Requests\MergeUsers
    {
        return $this->mergeUsersWithId($this->mapper->identifier($target_user), $this->mapper->identifier($source_user), ['cascadeCreate' => true]);
    }

    /**
     * Build Recombee value-setting requests from the supplied model.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     * @return \Recombee\RecommApi\Requests\SetItemValues
     */
    public function addItem(Model $item): \Recombee\RecommApi\Requests\SetItemValues
    {
        return $this->setItemValues($this->mapper->identifier($item), $this->mapper->values($item));
    }

    /**
     * Update an item in recombee db.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return \Recombee\RecommApi\Requests\SetItemValues
     */
    public function updateItem(Model $item): \Recombee\RecommApi\Requests\SetItemValues
    {
        return $this->addItem($item);
    }

    /**
     * Build Recombee value-setting requests from the supplied models.
     *
     * @param array<\Illuminate\Database\Eloquent\Model> $items
     * @return array<\Recombee\RecommApi\Requests\SetItemValues>
     */
    public function addItems(array $items): array
    {
        return array_map(function ($item) {
            return $this->addItem($item);
        }, $items);
    }

    /**
     * Recommend items to user.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function recommendTo(Authenticatable $user, int $limit = 10, array $options = []): mixed
    {
        return $this->recommendItemsToUser($this->mapper->identifier($user), $limit, $options);
    }

    /**
     * Defer the synchronous SDK call until the returned promise is awaited.
     *
     * SDK failures reject the promise with the original exception object.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function send(Request $request)
    {
        return $promise = new Promise(function () use (&$promise, $request) {
            try {
                $request->setTimeout($this->timeout);
                $response = $this->client->send($request);
                $promise->resolve($response);
            } catch (Throwable $e) {
                $promise->reject($e);
            }
        });
    }
}
