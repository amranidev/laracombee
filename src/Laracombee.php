<?php

namespace Amranidev\Laracombee;

use GuzzleHttp\Promise\Promise;
use Recombee\RecommApi\Exceptions;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Recombee\RecommApi\Requests\Request;

class Laracombee extends AbstractRecombee
{
    /**
     * Create new laracombee instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a user to recombee db.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return \Recombee\RecommApi\Requests\SetUserValues
     */
    public function addUser(User $user)
    {
        $laracombeeProperties = $user::$laracombee;

        $values = collect($user->toArray())->filter(function ($value, $key) use ($laracombeeProperties) {
            return isset($laracombeeProperties[$key]);
        })->all();

        return $this->setUserValues($user->id, $values);
    }

    /**
     * Add multiple users to recombee db.
     *
     * @param array $users
     *
     * @return array
     */
    public function addUsers(array $users)
    {
        return array_map(function ($user) {
            return $this->addUser($user);
        }, $users);
    }

    /**
     * Update a user in recombee.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return \Amranidev\Laracombee\AbstractRecombee;
     */
    public function updateUser(User $user)
    {
        return $this->addUser($user);
    }

    /**
     * Merge users.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return \Recombee\RecommApi\MergeUsers
     */
    public function mergeUsers(User $target_user, User $source_user)
    {
        return $this->mergeUsersWithId($target_user->id, $source_user->id, ['cascade_create' => true]);
    }

    /**
     * Add an item to recombee db.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return \Recombee\RecommApi\Requests\SetItemValues
     */
    public function addItem(Model $item)
    {
        $laracombeeProperties = $item::$laracombee;

        $values = collect($item->toArray())->filter(function ($value, $key) use ($laracombeeProperties) {
            return isset($laracombeeProperties[$key]);
        })->all();

        return $this->setItemValues($item->id, $values);
    }

    /**
     * Update an item in recombee db.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return \Recombee\RecommApi\Requests\AddItemProperty
     */
    public function updateItem(Model $item)
    {
        return $this->addItem($item);
    }

    /**
     * Add multiple items to recombee db.
     *
     * @param array $items
     *
     * @return array
     */
    public function addItems(array $items)
    {
        return array_map(function ($item) {
            return $this->addItem($item);
        }, $items);
    }

    /**
     * Recommend items to user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param int                              $limit
     * @param array                            $options
     *
     * @return \Recombee\RecommApi\Requests\Request $request
     */
    public function recommendTo(User $user, $limit = 10, $options = [])
    {
        return $this->recommendItemsToUser($user->id, $limit, $options);
    }

    /**
     * Send request.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function send(Request $request)
    {
        return $promise = new Promise(function () use (&$promise, $request) {
            try {
                $response = $this->client->send($request);
                $promise->resolve($response);
            } catch (Exceptions\ApiTimeoutException $e) {
                $promise->reject($e->getMessage());
            } catch (Exceptions\ResponseException $e) {
                $promise->reject($e->getMessage());
            } catch (Exceptions\ApiException $e) {
                $promise->reject($e->getMessage());
            }
        });
    }
}
