<?php

namespace Amranidev\Laracombee;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\AddUser;
use Recombee\RecommApi\Requests\DeleteItem;
use Recombee\RecommApi\Requests\DeleteUser;
use Recombee\RecommApi\Requests\ListUsers;
use Recombee\RecommApi\Requests\MergeUsers;
use Recombee\RecommApi\Requests\RecommendItemsToUser;
use Recombee\RecommApi\Requests\SetItemValues;
use Recombee\RecommApi\Requests\SetUserValues;
use Recombee\RecommApi\Requests\AddUserProperty;
use Recombee\RecommApi\Requests\AddItemProperty;
use Recombee\RecommApi\Requests\DeleteUserProperty;
use Recombee\RecommApi\Requests\DeleteItemProperty;
use Recombee\RecommApi\Requests\AddDetailedView;

// AddDetailedView

class AbstractRecombee
{
    /**
     * @var Recombee\RecommApi\Client
     */
    public $client;

    /**
     * @var int
     */
    public $timeout;

    /**
     * Create new Laracombee instance.
     */
    public function __construct()
    {
        $this->client = new Client(config('laracombee.database'), config('laracombee.token'));
        $this->timeout = config('laracombee.timeout');
    }

    /**
     * Add new item to recombee.
     *
     * @param int $item_id
     * @param array $fileds
     *
     * @return mixed
     */
    public function addItem($item_id, $fields)
    {
        $item = new SetItemValues($item_id, $fields, [
            "cascadeCreate" => true,
        ]);

        $item->setTimeout($this->timeout);

        $this->client->send($item);

        return true;
    }

    /**
     * Get recommended items.
     *
     * @param int $user_id
     * @param int $limit
     * @param array $filters
     *
     * @return mixed
     */
    public function recommendItemsToUser($user_id, $limit, $filters)
    {
        $items = new RecommendItemsToUser($user_id, $limit, $filters);

        $items->setTimeout($this->timeout);

        return $this->client->send($items);
    }

    /**
     * Update item.
     *
     * @param int $item_id
     * @param array $fileds
     *
     * @return mixed
     */
    public function updateItem($item_id, $fields)
    {
        return $this->addItem($item_id, $fields);
    }

    /**
     * Remove item.
     *
     * @param int $item_id
     *
     * @return mixed
     */
    public function removeItem($item_id)
    {
        $item = new DeleteItem($item_id);

        $item->setTimeout($this->timeout);

        $this->client->send($item);

        return true;
    }

    /**
     * Add new user to recombee.
     *
     * @param int $item_id
     *
     * @return mixed
     */
    public function addUser($user_id)
    {
        $user = new AddUser($user_id);

        $this->client->send($user);

        return true;
    }

    /**
     * Delete user.
     *
     * @param int $user_id
     *
     * @return mixed
     */
    public function deleteUser($user_id)
    {
        $user = new DeleteUser($user_id);

        $this->client->send($user);

        return true;
    }

    /**
     * Merge users.
     *
     * @param int $target_user_id
     * @param int $source_user_id
     * @param array $params
     *
     * @return mixed
     */
    public function mergeUsers($target_user_id, $source_user_id, $params)
    {
        $merge = new MergeUsers($target_user_id, $source_user_id, $params);

        $this->client->send($merge);

        return true;
    }

    /**
     * List Users.
     *
     * @param int $params
     *
     * @return mixed
     */
    public function listUsers($params)
    {
        $users = new listUsers($params);

        return $this->client->send($users);
    }

    /**
     * Set Users Values.
     */
    public function setUserValues(int $user_id, array $fileds)
    {
        $user = new SetUserValues($user_id, $fileds, [
            'cascadeCreate' => true,
        ]);

        return $this->client->send($user);
    }

    /**
     * Add user Property.
     */
    public function addUserProperty($property, $type)
    {
        $userProps =  new AddUserProperty($property, $type);

        $client->send($userProps);

        return true;
    }

    public function deleteUserProperty($property, $type)
    {
        $userProps =  new DeleteUserProperty($property, $type);

        $client->send($userProps);

        return true;
    }

    /**
     * Add item property.
     */
    public function addItemProperty($property, $type)
    {
        $itemProps = new AddItemProperty($property, $type);

        $clinet->send($itemProps);

        return true;
    }

    /**
     * Add item property.
     */
    public function deleteItemProperty($property, $type)
    {
        $itemProps = new DeleteItemProperty($property, $type);

        $clinet->send($itemProps);

        return true;
    }

    /**
     * Add Detailed View.
     * 
     * @var int $user_id
     * @var int $item_id
     * @var array $options
     */
    public function addDetailedView($user_id, $item_id, $options)
    {
        $detailedView = new AddDetailedView($user_id, $item_id, $options);

        $this->client->send($detailedView);

        return true;
    }
}
