<?php

namespace Amranidev\Laracombee;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Exceptions;
use Recombee\RecommApi\Requests\AddBookmark;
use Recombee\RecommApi\Requests\AddCartAddition;
use Recombee\RecommApi\Requests\AddDetailedView;
use Recombee\RecommApi\Requests\AddItemProperty;
use Recombee\RecommApi\Requests\AddPurchase;
use Recombee\RecommApi\Requests\AddRating;
use Recombee\RecommApi\Requests\AddUserProperty;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\DeleteBookmark;
use Recombee\RecommApi\Requests\DeleteCartAddition;
use Recombee\RecommApi\Requests\DeleteItem;
use Recombee\RecommApi\Requests\DeleteItemProperty;
use Recombee\RecommApi\Requests\DeleteItemView;
use Recombee\RecommApi\Requests\DeletePurchase;
use Recombee\RecommApi\Requests\DeleteUser;
use Recombee\RecommApi\Requests\DeleteUserProperty;
use Recombee\RecommApi\Requests\ListItemDetailViews;
use Recombee\RecommApi\Requests\ListUserDetailViews;
use Recombee\RecommApi\Requests\ListUsers;
use Recombee\RecommApi\Requests\MergeUsers;
use Recombee\RecommApi\Requests\RecommendItemsToUser;
use Recombee\RecommApi\Requests\RecommendUsersToUser;
use Recombee\RecommApi\Requests\Request;
use Recombee\RecommApi\Requests\SetItemValues;
use Recombee\RecommApi\Requests\SetUserValues;

class AbstractRecombee
{
    /**
     * @var Recombee\RecommApi\Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * Create new Laracombee instance.
     */
    public function __construct()
    {
        $this->client = new Client(config('laracombee.database'), config('laracombee.token'));
        $this->timeout = config('laracombee.timeout');
    }

    /**
     * Send request as bulk.
     *
     * @param array $bulk
     *
     * @return void
     */
    public function batch(array $bulk)
    {
        $batch = new Batch($bulk);

        $batch->setTimeout($this->timeout);

        $this->send($batch);
    }

    /**
     * Send request.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     *
     * @return void
     */
    public function send(Request $request)
    {
        try {
            $request = $this->client->send($request);
        } catch (Exceptions\ApiTimeoutException $e) {
            echo $e->getMessage();
        } catch (Exceptions\ResponseException $e) {
            echo $e->getMessage();
            // @todo
        } catch (Exceptions\ApiException $e) {
            echo $e->getMessage();
            // @todo
        }

        return $request;
    }

    /**
     * Add new item to recombee.
     *
     * @param int   $item_id
     * @param array $fileds
     *
     * @return \Recombee\RecommApi\SetItemValues
     */
    public function setItemValues($item_id, array $fields)
    {
        $item = new SetItemValues($item_id, $fields, [
            'cascadeCreate' => true,
        ]);

        $item->setTimeout($this->timeout);

        return $item;
    }

    /**
     * Get recommended items.
     *
     * @param int   $user_id
     * @param int   $limit
     * @param array $filters
     *
     * @return mixed
     */
    public function recommendItemsToUser($user_id, int $limit, array $filters)
    {
        $items = new RecommendItemsToUser($user_id, $limit, $filters);

        $items->setTimeout($this->timeout);

        return $this->send($items);
    }

    /**
     * Recommend Users to User.
     *
     * @param int   $user_id
     * @param int   $limit
     * @param array $filters
     *
     * @return mixed
     */
    public function recommendUsersToUser($usre_id, int $limit, array $filters)
    {
        $users = new RecommendUsersToUser($user_id, $limit, $filters);

        $items->setTimeout($this->timeout);

        return $this->send($users);
    }

    /**
     * Remove item.
     *
     * @param int $item_id
     *
     * @return \Recombee\RecommApi\DeleteItem
     */
    public function deleteItem($item_id)
    {
        $item = new DeleteItem($item_id);

        $item->setTimeout($this->timeout);

        return $item;
    }

    /**
     * Delete user.
     *
     * @param int $user_id
     *
     * @return \Recombee\RecommApi\DeleteUser
     */
    public function deleteUser($user_id)
    {
        $user = new DeleteUser($user_id);

        return $user;
    }

    /**
     * Merge users.
     *
     * @param int   $target_user_id
     * @param int   $source_user_id
     * @param array $params
     *
     * @return \Recombee\RecommApi\MergeUsers
     */
    public function mergeUsersWithId($target_user_id, $source_user_id, array $params)
    {
        $merge = new MergeUsers($target_user_id, $source_user_id, $params);

        return $merge;
    }

    /**
     * List Users.
     *
     * @param array $filters
     *
     * @return mixed
     */
    public function listUsers(array $filters)
    {
        $users = new listUsers($filters);

        $users->setTimeout($this->timeout);

        return $this->send($users);
    }

    /**
     * Set Users Values.
     *
     * @param int   $user_id
     * @param array $fields
     *
     * @return \Recombee\RecommApi\SetUserValues
     */
    public function setUserValues($user_id, array $fileds)
    {
        $user = new SetUserValues($user_id, $fileds, [
            'cascadeCreate' => true,
        ]);

        return $user;
    }

    /**
     * Add user Property.
     *
     * @param string $property
     * @param string $type
     *
     * @return \Recombee\RecommApi\AddUserProperty
     */
    public function addUserProperty(string $property, string $type)
    {
        $userProps = new AddUserProperty($property, $type);

        return $userProps;
    }

    /**
     * Delete User Property.
     *
     * @param string $property
     *
     * @return \Recombee\RecommApi\DeleteUserProperty
     */
    public function deleteUserProperty(string $property)
    {
        $userProps = new DeleteUserProperty($property);

        return $userProps;
    }

    /**
     * List User Detail Views.
     *
     * @param int $user_id
     *
     * @return \Recombee\RecommApi\ListUserDetailViews
     */
    public function listUserDetailViews($user_id)
    {
        $details = new ListUserDetailViews($user_id);

        return $details;
    }

    /**
     * Add item property.
     *
     * @param string $property
     * @param string $type
     *
     * @return \Recombee\RecommApi\AddItemProperty
     */
    public function addItemProperty(string $property, string $type)
    {
        $itemProps = new AddItemProperty($property, $type);

        return $itemProps;
    }

    /**
     * Add item property.
     *
     * @param string $property
     *
     * @return \Recombee\RecommApi\DeleteItemProperty
     */
    public function deleteItemProperty($property)
    {
        $itemProps = new DeleteItemProperty($property);

        return $itemProps;
    }

    /**
     * Add Detailed View.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\AddDetailedView
     */
    public function addDetailedView($user_id, $item_id, array $options)
    {
        $detailedView = new AddDetailedView($user_id, $item_id, $options);

        return $detailedView;
    }

    /**
     * Delete Item View.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\DeleteItemView
     */
    public function deleteItemView($user_id, $item_id, array $options)
    {
        $detailedView = new DeleteItemView($user_id, $item_id, $options);

        return $detailedView;
    }

    /**
     * List Item Detail Views.
     *
     * @param int $item_id
     *
     * @return \Recombee\RecommApi\ListItemDetailViews
     */
    public function listItemDetailViews($item_id)
    {
        $details = new ListItemDetailViews($item_id);

        return $details;
    }

    /**
     * Add Purchase.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\AddPurchase
     */
    public function addPurchase($user_id, $item_id, $options)
    {
        $purchase = new AddPurchase($user_id, $item_id, $options);

        return $purchase;
    }

    /**
     * Delete Purchase.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\DeletePurchase
     */
    public function deletePurchase($user_id, $item_id, array $options)
    {
        $purchase = new DeletePurchase($user_id, $item_id, $options);

        return $purchase;
    }

    /**
     * Add rating.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param int   $rating
     * @param array $options
     *
     * @return \Recombee\RecommApi\AddRating
     */
    public function addRating($user_id, $item_id, int $rating, array $options)
    {
        $rating = new AddRating($user_id, $item_id, $rating, $options);

        return $rating;
    }

    /**
     * Delete rating.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\DeleteRating
     */
    public function deleteRating($user_id, $item_id, array $options)
    {
        $rating = new DeleteRating($user_id, $item_id, $options);

        return $rating;
    }

    /**
     * Card Addtion.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\AddCartAddition
     */
    public function addCartAddition($user_id, $item_id, array $options)
    {
        $addition = new AddCartAddition($user_id, $item_id, $options);

        return $addition;
    }

    /**
     * Delete Card Addtion.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\DeleteCartAddition
     */
    public function deleteCartAddition($user_id, $item_id, array $options)
    {
        $addition = new DeleteCartAddition($user_id, $item_id, $options);

        return $addition;
    }

    /**
     * Add Bookmark.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\AddBookmark
     */
    public function addBookmark($user_id, $item_id, array $options)
    {
        $bookmark = new AddBookmark($user_id, $item_id, $options);

        return $bookmark;
    }

    /**
     * Delete Bookmark.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\DeleteBookmark
     */
    public function deleteBookmark($user_id, $item_id, array $options)
    {
        $bookmark = new DeleteBookmark($user_id, $item_id, $options);

        return $bookmark;
    }
}
