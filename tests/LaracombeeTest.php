<?php

namespace Amranidev\Laracombee\Tests;

use Laracombee;
use Carbon\Carbon;
use Recombee\RecommApi\Requests\Request;

/**
 * Verify request construction and response handling using deterministic API responses.
 */
class LaracombeeTest extends TestCase
{
    /**
     * The expected response for successful fake API mutations.
     *
     * @var string
     */
    public $recombeeResponse = 'ok';

    /**
     * The user identifier used in request fixtures.
     *
     * @var int
     */
    public $userId = 1;

    /**
     * The item identifier used in request fixtures.
     *
     * @var int
     */
    public $itemId = 1;

    /**
     * The ISO 8601 timestamp prepared for the current test.
     *
     * @var string
     */
    public $timestamp;

    /**
     * Prepare the application and fixtures before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->timestamp = Carbon::now()->toIso8601String();
    }

    /**
     * Verify add item property request construction and fake response handling.
     *
     * @return void
     */
    public function testAddItemProperty()
    {
        $request = Laracombee::addItemProperty('productName', 'string');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify add user property request construction and fake response handling.
     *
     * @return void
     */
    public function testAddUserProperty()
    {
        $request = Laracombee::addUserProperty('firstName', 'string');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify add user request construction and fake response handling.
     *
     * @return void
     */
    public function testAddUser()
    {
        $userProperties = ['firstName' => 'Jhon Doe'];

        $request = Laracombee::setUserValues($this->userId, $userProperties);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify get user values request construction and fake response handling.
     *
     * @return void
     */
    public function testGetUserValues()
    {
        $request = Laracombee::getUserValues($this->userId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertIsArray($response);
    }

    /**
     * Verify add item request construction and fake response handling.
     *
     * @return void
     */
    public function testAddItem()
    {
        $itemProperties = ['productName' => 'My product'];

        $request = Laracombee::setItemValues($this->itemId, $itemProperties);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify get item values request construction and fake response handling.
     *
     * @return void
     */
    public function testGetItemValues()
    {
        $request = Laracombee::getItemValues($this->itemId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertIsArray($response);
    }

    /**
     * Verify add detail view request construction and fake response handling.
     *
     * @return void
     */
    public function testAddDetailView()
    {
        $options = ['duration' => 15, 'cascadeCreate' => true];

        $request = Laracombee::addDetailView($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify purchase request construction and a successful fake response.
     *
     * @return void
     */
    public function testAddandDeletePurchase()
    {
        $time = (float) Carbon::now()->timestamp.'.0';
        $options = [
            'timestamp'     => $time,
            'cascadeCreate' => true,
            'amount'        => 5,
            'price'         => 15,
            'profit'        => 20,
        ];

        $request = Laracombee::addPurchase($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify add rating request construction and fake response handling.
     *
     * @return void
     */
    public function testAddRating()
    {
        $options = [
            'cascadeCreate' => true,
        ];

        // rating shoud be a real number between  -1.0 < x < 1.0
        $rating = 0.8;

        $request = Laracombee::addRating($this->userId, $this->itemId, (float) $rating, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify cart-addition request construction and a successful fake response.
     *
     * @return void
     */
    public function testAddCardAddition()
    {
        $options = [
            'cascadeCreate' => true,
            'amount'        => 5,
            'price'         => 50,
        ];

        $request = Laracombee::addCartAddition($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify add bookmark request construction and fake response handling.
     *
     * @return void
     */
    public function testAddBookmark()
    {
        $options = [
            'cascadeCreate' => true,
        ];

        $request = Laracombee::addBookmark($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify recommend items to user.
     *
     * @return void
     */
    public function testRecommendItemsToUser()
    {
        $filter = [];

        $response = Laracombee::recommendItemsToUser($this->userId, 1, $filter)->wait();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('recomms', $response);
        $this->assertArrayHasKey('recommId', $response);
    }

    /**
     * Verify list user detail views request construction and fake response handling.
     *
     * @return void
     */
    public function testListUserDetailViews()
    {
        $details = Laracombee::listUserDetailViews($this->userId);

        $response = Laracombee::send($details)->wait();

        $this->assertInstanceOf(Request::class, $details);
        $this->assertIsArray($response);
    }

    /**
     * Verify list item detail views request construction and fake response handling.
     *
     * @return void
     */
    public function testListItemDetailViews()
    {
        $details = Laracombee::listItemDetailViews($this->itemId);

        $response = Laracombee::send($details)->wait();

        $this->assertInstanceOf(Request::class, $details);
        $this->assertIsArray($response);
    }

    /**
     * Verify list items request construction and fake response handling.
     *
     * @return void
     */
    public function testListItems()
    {
        $options = [
            'filter'             => '',
            'count'              => 5,
            'offset'             => 0,
            'returnProperties'   => true,
            'includedProperties' => ['productName'],
        ];

        $request = Laracombee::listItems($options);

        $response = Laracombee::send($request)->wait();

        $this->assertIsArray($response);

        foreach ($response as $item) {
            $this->assertArrayHasKey('productName', $item);
            $this->assertArrayHasKey('itemId', $item);
        }
    }

    /**
     * Verify get item property info request construction and fake response handling.
     *
     * @return void
     */
    public function testGetItemPropertyInfo()
    {
        $request = Laracombee::getItemPropertyInfo('productName');

        $response = Laracombee::send($request)->wait();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals('productName', $response['name']);
        $this->assertEquals('string', $response['type']);
    }

    /**
     * Verify list users request construction and fake response handling.
     *
     * @return void
     */
    public function testListUsers()
    {
        $options = [
            'filter'             => '',
            'count'              => 5,
            'offset'             => 0,
            'returnProperties'   => true,
            'includedProperties' => ['firstName'],
        ];

        $request = Laracombee::listUsers($options);

        $response = Laracombee::send($request)->wait();

        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
        $this->assertInstanceOf(Request::class, $request);

        foreach ($response as $user) {
            $this->assertArrayHasKey('firstName', $user);
            $this->assertArrayHasKey('userId', $user);
        }
    }

    /**
     * Verify list item ratings request construction and fake response handling.
     *
     * @return void
     */
    public function testListItemRatings()
    {
        $request = Laracombee::listItemRatings($this->itemId);
        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertIsArray($response);
    }

    /**
     * Verify list user ratings request construction and fake response handling.
     *
     * @return void
     */
    public function testListUserRatings()
    {
        $request = Laracombee::listUserRatings($this->userId);
        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertIsArray($response);
    }

    /**
     * Verify add series request construction and fake response handling.
     *
     * @return void
     */
    public function testAddSeries()
    {
        $request = Laracombee::addSeries('laracombee-series');

        $response = Laracombee::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify insert to series request construction and fake response handling.
     *
     * @return void
     */
    public function testInsertToSeries()
    {
        $request = Laracombee::insertToSeries('laracombee-series', 'item', (string) $this->itemId, 200);

        $response = Laracombee::send($request)->wait();

        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify list series request construction and fake response handling.
     *
     * @return void
     */
    public function testListSeries()
    {
        $request = Laracombee::listSeries();
        $response = Laracombee::send($request)->wait();

        $this->assertIsArray($response);
    }

    /**
     * Verify list series items request construction and fake response handling.
     *
     * @return void
     */
    public function testListSeriesItems()
    {
        $request = Laracombee::listSeriesItems('laracombee-series');
        $response = Laracombee::send($request)->wait();

        $this->assertIsArray($response);
    }

    /**
     * Verify remove from series request construction and fake response handling.
     *
     * @return void
     */
    public function testRemoveFromSeries()
    {
        $request = Laracombee::removeFromSeries('laracombee-series', 'item', $this->itemId, 200);
        $response = Laracombee::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify delete series request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteSeries()
    {
        $request = Laracombee::deleteSeries('laracombee-series');
        $response = Laracombee::send($request)->wait();
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify set view portion request construction and fake response handling.
     *
     * @return void
     */
    public function testSetViewPortion()
    {
        $request = Laracombee::setViewPortion($this->userId, $this->itemId, 0.22, []);
        $response = Laracombee::send($request)->wait();
        $this->assertEquals($response, 'ok');
    }

    /**
     * Verify delete view portion request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteViewPortion()
    {
        $request = Laracombee::deleteViewPortion($this->userId, $this->itemId, []);
        $response = Laracombee::send($request)->wait();
        $this->assertEquals($response, $this->recombeeResponse);
    }

    // public function testDeleteRating()
    // {
    //     $options = [];

    //     $request = Laracombee::deleteRating($this->userId, $this->itemId, $options);

    //     $response = Laracombee::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteCardAddition()
    // {
    //     $options = [];

    //     $request = Laracombee::deleteCartAddition($this->userId, $this->itemId, $options);

    //     $response = Laracombee::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteBookmark()
    // {
    //     $options = [];

    //     $request = Laracombee::deleteBookmark($this->userId, $this->itemId, $options);

    //     $response = Laracombee::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteDetailView()
    // {
    //     $options = [];

    //     $request = Laracombee::deleteDetailView($this->userId, $this->itemId, $options);

    //     $response = Laracombee::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    /**
     * Verify delete user request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteUser()
    {
        $request = Laracombee::deleteUser($this->userId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify delete item request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteItem()
    {
        $request = Laracombee::deleteItem($this->itemId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify delete user property request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteUserProperty()
    {
        $request = Laracombee::deleteUserProperty('firstName');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify delete item property request construction and fake response handling.
     *
     * @return void
     */
    public function testDeleteItemProperty()
    {
        $request = Laracombee::deleteItemProperty('productName');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    /**
     * Verify reset database request construction and fake response handling.
     *
     * @return void
     */
    public function testResetDatabase()
    {
        $request = Laracombee::resetDatabase();

        $response = Laracombee::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }
}
