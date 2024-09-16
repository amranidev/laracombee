<?php

namespace Amranidev\Laracombee\Tests;

use Amranidev\LaracombeeFacade\Facades\LaracombeeFacade;
use Carbon\Carbon;

class LaracombeeTest extends TestCase
{
    public $recombeeResponse = 'ok';

    public $userId = 1;

    public $itemId = 1;

    public $timestamp;

    public function setUp(): void
    {
        parent::setUp();
        $this->timestamp = Carbon::now()->toIso8601String();
    }

    public function testAddItemProperty()
    {
        $request = LaracombeeFacade::addItemProperty('productName', 'string');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUserProperty()
    {
        $request = LaracombeeFacade::addUserProperty('firstName', 'string');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUser()
    {
        $userProperties = ['firstName' => 'Jhon Doe'];

        $request = LaracombeeFacade::setUserValues($this->userId, $userProperties);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testGetUserValues()
    {
        $request = LaracombeeFacade::getUserValues($this->userId);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertIsArray($response);
    }

    public function testAddItem()
    {
        $itemProperties = ['productName' => 'My product'];

        $request = LaracombeeFacade::setItemValues($this->itemId, $itemProperties);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testGetItemValues()
    {
        $request = LaracombeeFacade::getItemValues($this->itemId);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertIsArray($response);
    }

    public function testAddDetailView()
    {
        $options = ['duration' => 15, 'cascadeCreate' => true];

        $request = LaracombeeFacade::addDetailView($this->userId, $this->itemId, $options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

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

        $request = LaracombeeFacade::addPurchase($this->userId, $this->itemId, $options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testAddRating()
    {
        $options = [
            'cascadeCreate' => true,
        ];

        // rating shoud be a real number betweed  -1.0 < x < 1.0
        $rating = 0.8;

        $request = LaracombeeFacade::addRating($this->userId, $this->itemId, (float) $rating, $options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testAddCardAddition()
    {
        $options = [
            'cascadeCreate' => true,
            'amount'        => 5,
            'price'         => 50,
        ];

        $request = LaracombeeFacade::addCartAddition($this->userId, $this->itemId, $options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testAddBookmark()
    {
        $options = [
            'cascadeCreate' => true,
        ];

        $request = LaracombeeFacade::addBookmark($this->userId, $this->itemId, $options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testRecommendItemsToUser()
    {
        $filter = [];

        $response = LaracombeeFacade::recommendItemsToUser($this->userId, 1, $filter)->wait();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('recomms', $response);
        $this->assertArrayHasKey('recommId', $response);
    }

    public function testListUserDetailViews()
    {
        $details = LaracombeeFacade::listUserDetailViews($this->userId);

        $response = LaracombeeFacade::send($details)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $details);
        $this->assertIsArray($response);
    }

    public function testListItemDetailViews()
    {
        $details = LaracombeeFacade::listItemDetailViews($this->itemId);

        $response = LaracombeeFacade::send($details)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $details);
        $this->assertIsArray($response);
    }

    public function testListItems()
    {
        $options = [
            'filter'             => '',
            'count'              => 5,
            'offset'             => 0,
            'returnProperties'   => true,
            'includedProperties' => ['productName'],
        ];

        $request = LaracombeeFacade::listItems($options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertIsArray($response);

        foreach ($response as $item) {
            $this->assertArrayHasKey('productName', $item);
            $this->assertArrayHasKey('itemId', $item);
        }
    }

    public function testGetItemPropertyInfo()
    {
        $request = LaracombeeFacade::getItemPropertyInfo('productName');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals('productName', $response['name']);
        $this->assertEquals('string', $response['type']);
    }

    public function testListUsers()
    {
        $options = [
            'filter'             => '',
            'count'              => 5,
            'offset'             => 0,
            'returnProperties'   => true,
            'includedProperties' => ['firstName'],
        ];

        $request = LaracombeeFacade::listUsers($options);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);

        foreach ($response as $user) {
            $this->assertArrayHasKey('firstName', $user);
            $this->assertArrayHasKey('userId', $user);
        }
    }

    public function testListItemRatings()
    {
        $request = LaracombeeFacade::listItemRatings($this->itemId);
        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertIsArray($response);
    }

    public function testListUserRatings()
    {
        $request = LaracombeeFacade::listUserRatings($this->userId);
        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertIsArray($response);
    }

    public function testAddSeries()
    {
        $request = LaracombeeFacade::addSeries('LaracombeeFacade-series');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testInsertToSeries()
    {
        $request = LaracombeeFacade::insertToSeries('LaracombeeFacade-series', 'item', (string) $this->itemId, 200);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertEquals($response, 'ok');
    }

    public function testListSeries()
    {
        $request = LaracombeeFacade::listSeries();
        $response = LaracombeeFacade::send($request)->wait();

        $this->assertIsArray($response);
    }

    public function testListSeriesItems()
    {
        $request = LaracombeeFacade::listSeriesItems('LaracombeeFacade-series');
        $response = LaracombeeFacade::send($request)->wait();

        $this->assertIsArray($response);
    }

    public function testRemoveFromSeries()
    {
        $request = LaracombeeFacade::removeFromSeries('LaracombeeFacade-series', 'item', $this->itemId, 200);
        $response = LaracombeeFacade::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteSeries()
    {
        $request = LaracombeeFacade::deleteSeries('LaracombeeFacade-series');
        $response = LaracombeeFacade::send($request)->wait();
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testSetViewPortion()
    {
        $request = LaracombeeFacade::setViewPortion($this->userId, $this->itemId, 0.22, []);
        $response = LaracombeeFacade::send($request)->wait();
        $this->assertEquals($response, 'ok');
    }

    public function testDeleteViewPortion()
    {
        $request = LaracombeeFacade::deleteViewPortion($this->userId, $this->itemId, []);
        $response = LaracombeeFacade::send($request)->wait();
        $this->assertEquals($response, $this->recombeeResponse);
    }

    // public function testDeleteRating()
    // {
    //     $options = [];

    //     $request = LaracombeeFacade::deleteRating($this->userId, $this->itemId, $options);

    //     $response = LaracombeeFacade::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteCardAddition()
    // {
    //     $options = [];

    //     $request = LaracombeeFacade::deleteCartAddition($this->userId, $this->itemId, $options);

    //     $response = LaracombeeFacade::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteBookmark()
    // {
    //     $options = [];

    //     $request = LaracombeeFacade::deleteBookmark($this->userId, $this->itemId, $options);

    //     $response = LaracombeeFacade::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    // public function testDeleteDetailView()
    // {
    //     $options = [];

    //     $request = LaracombeeFacade::deleteDetailView($this->userId, $this->itemId, $options);

    //     $response = LaracombeeFacade::send($request)->wait();

    //     $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
    //     $this->assertEquals($response, $this->recombeeResponse);
    // }

    public function testDeleteUser()
    {
        $request = LaracombeeFacade::deleteUser($this->userId);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteItem()
    {
        $request = LaracombeeFacade::deleteItem($this->itemId);

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteUserProperty()
    {
        $request = LaracombeeFacade::deleteUserProperty('firstName');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteItemProperty()
    {
        $request = LaracombeeFacade::deleteItemProperty('productName');

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testResetDatabase()
    {
        $request = LaracombeeFacade::resetDatabase();

        $response = LaracombeeFacade::send($request)->wait();

        $this->assertEquals($response, $this->recombeeResponse);
    }
}