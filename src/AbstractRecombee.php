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
use Recombee\RecommApi\Requests\DeleteItemView;
use Recombee\RecommApi\Requests\AddPurchase;
use Recombee\RecommApi\Requests\DeletePurchase;
use Recombee\RecommApi\Requests\AddRating;
use Recombee\RecommApi\Requests\AddCartAddition;
use Recombee\RecommApi\Requests\DeleteCartAddition;
use Recombee\RecommApi\Requests\AddBookmark;
use Recombee\RecommApi\Requests\DeleteBookmark;

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
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function addDetailedView($user_id, $item_id, $options)
    {
        $detailedView = new AddDetailedView($user_id, $item_id, $options);

        $this->client->send($detailedView);

        return true;
    }

    /**
     * Delete Item View.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function deleteItemView($user_id, $item_id, $options)
    {
        $detailedView = new DeleteItemView($user_id, $item_id, $options);

        $this->client->send($detailedView);

        return true;
    }

    /**
     * Add Purchase.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function addPurchase($user_id, $item_id, $options)
    {
        $purchase = new AddPurchase($user_id, $item_id, $options);

        $this->client->send($purchase);

        return true;
    }

    /**
     * Delete Purchase.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function deletePurchase($user_id, $item_id, $options)
    {
        $purchase = new DeletePurchase($user_id, $item_id, $options);

        $this->client->send($purchase);

        return true;
    }

    /**
     * Add rating.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param int $rating
     * @param array $options
     */
    public function addRating($user_id, $item_id, $rating, $options)
    {
        $rating = new AddRating($user_id, $item_id, $rating, $options);

        $this->client->send($purchase);

        return true;
    }

    /**
     * Delete rating.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function deleteRating($user_id, $item_id, $options)
    {
        $rating = new DeleteRating($user_id, $item_id, $options);
        
        $this->client->send($rating);

        return true;
    }

    /**
     * Card Addtion.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function addCartAddition($user_id, $item_id, $options)
    {
        $addition = new AddCartAddition($user_id, $item_id, $options);

        $this->client->send($addition);

        return true;
    }

    /**
     * Delete Card Addtion.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function deleteCartAddition($user_id, $item_id, $options)
    {
        $addition = new DeleteCartAddition($user_id, $item_id, $options);

        $this->client->send($addition);

        return true;
    }

    /**
     * Add Bookmark.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function addBookmark($user_id, $item_id, $options)
    {
        $bookmark = new AddBookmark($user_id, $item_id, $options);

        $this->client->send($bookmark);

        return true;
    }

    /**
     * Delete Bookmark.
     * 
     * @param int $user_id
     * @param int $item_id
     * @param array $options
     */
    public function deleteBookmark($user_id, $item_id, $options)
    {
        $bookmark = new DeleteBookmark($user_id, $item_id, $options);

        $this->client->send($bookmark);

        return true;
    }
}
