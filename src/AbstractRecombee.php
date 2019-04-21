<?php

namespace Amranidev\Laracombee;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\Request;
use Recombee\RecommApi\Requests\AddRating;
use Recombee\RecommApi\Requests\AddSeries;
use Recombee\RecommApi\Requests\ListItems;
use Recombee\RecommApi\Requests\ListUsers;
use Recombee\RecommApi\Requests\DeleteItem;
use Recombee\RecommApi\Requests\DeleteUser;
use Recombee\RecommApi\Requests\ListSeries;
use Recombee\RecommApi\Requests\MergeUsers;
use Recombee\RecommApi\Requests\AddBookmark;
use Recombee\RecommApi\Requests\AddPurchase;
use Recombee\RecommApi\Requests\DeleteRating;
use Recombee\RecommApi\Requests\DeleteSeries;
use Recombee\RecommApi\Requests\AddDetailView;
use Recombee\RecommApi\Requests\GetItemValues;
use Recombee\RecommApi\Requests\GetUserValues;
use Recombee\RecommApi\Requests\ResetDatabase;
use Recombee\RecommApi\Requests\SetItemValues;
use Recombee\RecommApi\Requests\SetUserValues;
use Recombee\RecommApi\Requests\DeleteBookmark;
use Recombee\RecommApi\Requests\DeletePurchase;
use Recombee\RecommApi\Requests\InsertToSeries;
use Recombee\RecommApi\Requests\SetViewPortion;
use Recombee\RecommApi\Requests\AddCartAddition;
use Recombee\RecommApi\Requests\AddItemProperty;
use Recombee\RecommApi\Requests\AddUserProperty;
use Recombee\RecommApi\Requests\ListItemRatings;
use Recombee\RecommApi\Requests\ListSeriesItems;
use Recombee\RecommApi\Requests\ListUserRatings;
use Recombee\RecommApi\Requests\DeleteDetailView;
use Recombee\RecommApi\Requests\RemoveFromSeries;
use Recombee\RecommApi\Requests\DeleteViewPortion;
use Recombee\RecommApi\Requests\DeleteCartAddition;
use Recombee\RecommApi\Requests\DeleteItemProperty;
use Recombee\RecommApi\Requests\DeleteUserProperty;
use Recombee\RecommApi\Requests\GetItemPropertyInfo;
use Recombee\RecommApi\Requests\ListItemDetailViews;
use Recombee\RecommApi\Requests\ListUserDetailViews;
use Recombee\RecommApi\Requests\ListItemViewPortions;
use Recombee\RecommApi\Requests\ListUserViewPortions;
use Recombee\RecommApi\Requests\RecommendItemsToUser;
use Recombee\RecommApi\Requests\RecommendUsersToUser;

abstract class AbstractRecombee
{
    /**
     * @var \Recombee\RecommApi\Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * AbstractRecombee constructor.
     *
     * @param $database_id
     * @param $token
     * @param string $protocol
     * @param int    $timeout
     * @param array  $options
     */
    public function __construct(string $database_id, string $token, string $protocol = 'http', int $timeout = 1000, array $options = [])
    {
        $this->client = new Client($database_id, $token, $protocol, $options);
        $this->timeout = $timeout;
    }

    /**
     * Send request as bulk.
     *
     * @param array $bulk
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function batch(array $bulk)
    {
        $batch = new Batch($bulk);

        return $this->send($batch);
    }

    /**
     * Send request.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     *
     * @return mixed
     */
    abstract public function send(Request $request);

    /**
     * Add new item to recombee.
     *
     * @param string $item_id
     * @param array  $fields
     *
     * @return \Recombee\RecommApi\Requests\SetItemValues
     */
    public function setItemValues($item_id, array $fields)
    {
        $item = new SetItemValues($item_id, $fields, [
            'cascadeCreate' => true,
        ]);

        return $item;
    }

    /**
     * Get item values.
     *
     * @param string $item_id
     *
     * @return \Recombee\RecommApi\Requests\GetItemValues
     */
    public function getItemValues($item_id)
    {
        $values = new GetItemValues($item_id);

        return $values;
    }

    /**
     * Recommend items to user.
     *
     * @param int   $user_id
     * @param int   $limit
     * @param array $filters
     *
     * @return mixed
     */
    public function recommendItemsToUser($user_id, int $limit = 10, array $filters = [])
    {
        $items = new RecommendItemsToUser($user_id, $limit, $filters);

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
    public function recommendUsersToUser($user_id, int $limit = 10, array $filters = [])
    {
        $users = new RecommendUsersToUser($user_id, $limit, $filters);

        return $this->send($users);
    }

    /**
     * Remove item.
     *
     * @param int $item_id
     *
     * @return \Recombee\RecommApi\Requests\DeleteItem
     */
    public function deleteItem($item_id)
    {
        $item = new DeleteItem($item_id);

        return $item;
    }

    /**
     * List items.
     *
     * @param array $options
     *
     * @return \Recombee\RecommApi\Requests\ListItems
     */
    public function listItems(array $options = [])
    {
        $items = new ListItems($options);

        return $items;
    }

    /**
     * Get item property info.
     *
     * @param string $property_name
     *
     * @return \Recombee\RecommApi\Requests\GetItemPropertyInfo
     */
    public function getItemPropertyInfo(string $property_name)
    {
        $info = new GetItemPropertyInfo($property_name);

        return $info;
    }

    /**
     * Delete user.
     *
     * @param string $user_id
     *
     * @return \Recombee\RecommApi\Requests\DeleteUser
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
     * @return \Recombee\RecommApi\Requests\MergeUsers
     */
    public function mergeUsersWithId($target_user_id, $source_user_id, array $params = [])
    {
        $merge = new MergeUsers($target_user_id, $source_user_id, $params);

        return $merge;
    }

    /**
     * List Users.
     *
     * @param array $options
     *
     * @return \Recombee\RecommApi\Requests\ListUsers
     */
    public function listUsers(array $options = [])
    {
        $users = new listUsers($options);

        return $users;
    }

    /**
     * Set Users Values.
     *
     * @param int   $user_id
     * @param array $fields
     *
     * @return \Recombee\RecommApi\Requests\SetUserValues
     */
    public function setUserValues($user_id, array $fields)
    {
        $user = new SetUserValues($user_id, $fields, [
            'cascadeCreate' => true,
        ]);

        return $user;
    }

    /**
     * Get Users Values.
     *
     * @param string $user_id
     *
     * @return \Recombee\RecommApi\Requests\GetUserValues
     */
    public function getUserValues($user_id)
    {
        $values = new GetUserValues($user_id);

        return $values;
    }

    /**
     * Add user Property.
     *
     * @param string $property
     * @param string $type
     *
     * @return \Recombee\RecommApi\Requests\AddUserProperty
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
     * @return \Recombee\RecommApi\Requests\DeleteUserProperty
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
     * @return \Recombee\RecommApi\Requests\ListUserDetailViews
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
     * @return \Recombee\RecommApi\Requests\AddItemProperty
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
     * @return \Recombee\RecommApi\Requests\DeleteItemProperty
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
     * @return \Recombee\RecommApi\Requests\AddDetailView
     */
    public function addDetailView($user_id, $item_id, array $options = [])
    {
        $detailedView = new AddDetailView($user_id, $item_id, $options);

        return $detailedView;
    }

    /**
     * Delete Item View.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\Requests\DeleteDetailView
     */
    public function deleteDetailView($user_id, $item_id, array $options = [])
    {
        $detailedView = new DeleteDetailView($user_id, $item_id, $options);

        return $detailedView;
    }

    /**
     * List Item Detail Views.
     *
     * @param int $item_id
     *
     * @return \Recombee\RecommApi\Requests\ListItemDetailViews
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
     * @return \Recombee\RecommApi\Requests\AddPurchase
     */
    public function addPurchase($user_id, $item_id, array $options = [])
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
     * @return \Recombee\RecommApi\Requests\DeletePurchase
     */
    public function deletePurchase($user_id, $item_id, array $options = [])
    {
        $purchase = new DeletePurchase($user_id, $item_id, $options);

        return $purchase;
    }

    /**
     * Add rating.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param float $rating
     * @param array $options
     *
     * @return \Recombee\RecommApi\Requests\AddRating
     */
    public function addRating($user_id, $item_id, float $rating, array $options = [])
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
     * @return \Recombee\RecommApi\Requests\DeleteRating
     */
    public function deleteRating($user_id, $item_id, array $options = [])
    {
        $rating = new DeleteRating($user_id, $item_id, $options);

        return $rating;
    }

    /**
     * List item ratings.
     *
     * @param int $item_id
     *
     * @return \Recombee\RecommApi\Requests\ListItemRatings
     */
    public function listItemRatings($item_id)
    {
        $ratings = new ListItemRatings($item_id);

        return $ratings;
    }

    /**
     * list user ratings.
     *
     * @param int $user_id
     *
     * @return \Recombee\RecommApi\Requests\ListUserRatings
     */
    public function listUserRatings($user_id)
    {
        $ratings = new ListUserRatings($user_id);

        return $ratings;
    }

    /**
     * Card Addtion.
     *
     * @param int   $user_id
     * @param int   $item_id
     * @param array $options
     *
     * @return \Recombee\RecommApi\Requests\AddCartAddition
     */
    public function addCartAddition($user_id, $item_id, array $options = [])
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
     * @return \Recombee\RecommApi\Requests\DeleteCartAddition
     */
    public function deleteCartAddition($user_id, $item_id, array $options = [])
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
     * @return \Recombee\RecommApi\Requests\AddBookmark
     */
    public function addBookmark($user_id, $item_id, array $options = [])
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
     * @return \Recombee\RecommApi\Requests\DeleteBookmark
     */
    public function deleteBookmark($user_id, $item_id, array $options = [])
    {
        $bookmark = new DeleteBookmark($user_id, $item_id, $options);

        return $bookmark;
    }

    /**
     * Add Series.
     *
     * @param string $series_id
     *
     * @return \Recombee\RecommApi\Requests\AddSeries
     */
    public function addSeries(string $series_id)
    {
        $series = new AddSeries($series_id);

        return $series;
    }

    /**
     * Insert item to series.
     *
     * @param string $series_id
     * @param string $item_type
     * @param string $item_id
     * @param int    $time
     *
     * @return \Recombee\RecommApi\Requests\InsertToSeries
     */
    public function insertToSeries($series_id, string $item_type, $item_id, $time)
    {
        $series = new InsertToSeries($series_id, $item_type, $item_id, $time, []);

        return $series;
    }

    /**
     * Remove item form series.
     *
     * @param string $series_id
     * @param string $item_type
     * @param string $item_id
     * @param int    $time
     *
     * @return \Recombee\RecommApi\Requests\RemoveFromSeries
     */
    public function removeFromSeries($series_id, $item_type, $item_id, $time)
    {
        $removeFromSeries = new RemoveFromSeries($series_id, $item_type, $item_id, $time);

        return $removeFromSeries;
    }

    /**
     * Delete Series.
     *
     * @param string $series_id
     *
     * @return \Recombee\RecommApi\Requests\DeleteSeries
     */
    public function deleteSeries(string $series_id)
    {
        $series = new DeleteSeries($series_id);

        return $series;
    }

    /**
     * List Series.
     *
     * @return \Recombee\RecommApi\Requests\ListSeries
     */
    public function listSeries()
    {
        $series = new ListSeries();

        return $series;
    }

    /**
     * List Series items.
     *
     * @param string $series_id
     *
     * @return \Recombee\RecommApi\Requests\ListSeriesItems
     */
    public function listSeriesItems($series_id)
    {
        $items = new ListSeriesItems($series_id);

        return $items;
    }

    /**
     * Set view portion.
     *
     * @param string $userId
     * @param string $itemId
     * @param float  $portion
     * @param array  $options
     *
     * @return \Recombee\RecommApi\Requests\SetViewPortion
     */
    public function setViewPortion($userId, $itemId, float $portion, array $options = [])
    {
        $viewPortion = new SetViewPortion($userId, $itemId, $portion, $options);

        return $viewPortion;
    }

    /**
     * Delete view portion.
     *
     * @param string $userId
     * @param string $itemId
     * @param array  $options
     *
     * @return \Recombee\RecommApi\Requests\DeleteViewPortion
     */
    public function deleteViewPortion($userId, $itemId, array $options = [])
    {
        $deleteViewPortion = new DeleteViewPortion($userId, $itemId, $options);

        return $deleteViewPortion;
    }

    /**
     * List item view portions.
     *
     * @param string $itemId
     *
     * @return \Recombee\RecommApi\Requests\ListItemViewPortions
     */
    public function listItemViewPortions($itemId)
    {
        $listPortions = new ListItemViewPortions($itemId);

        return $listPortions;
    }

    /**
     * List user view portions.
     *
     * @param string $userId
     *
     * @return \Recombee\RecommApi\Requests\ListUserViewPortions
     */
    public function listUserViewPortions($userId)
    {
        $listPortions = new ListUserViewPortions($userId);

        return $listPortions;
    }

    /**
     * Reset database.
     *
     * @return \Recombee\RecommApi\Requests\ResetDatabase
     */
    public function resetDatabase()
    {
        $reset = new ResetDatabase();

        return $reset;
    }
}
