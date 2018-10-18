<?php

namespace Amranidev\Laracombee\Tests;

use Laracombee;
use Carbon\Carbon;

class LaracombeeTest extends TestCase
{
    public $recombeeResponse = '"ok"';

    public $userId = 1;

    public $itemId = 1;

    public $timestamp;

    public function setUp()
    {
        parent::setUp();
        $this->timestamp = Carbon::now()->toIso8601String();
    }

    public function testAddItemProperty()
    {
        $request = Laracombee::addItemProperty('productName', 'string');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUserProperty()
    {
        $request = Laracombee::addUserProperty('firstName', 'string');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUser()
    {
        $userProperties = ['firstName' => 'Jhon Doe'];

        $request = Laracombee::setUserValues($this->userId, $userProperties);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testGetUserValues()
    {
        $request = Laracombee::getUserValues($this->userId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertInternalType('array', $response);
    }

    public function testAddItem()
    {
        $itemProperties = ['productName' => 'My product'];

        $request = Laracombee::setItemValues($this->itemId, $itemProperties);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testGetItemValues()
    {
        $request = Laracombee::getItemValues($this->itemId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertInternalType('array', $response);
    }

    public function testAddDetailedView()
    {
        $options = ['duration' => 15, 'cascadeCreate' => true];

        $request = Laracombee::addDetailedView($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

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

        $request = Laracombee::addPurchase($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

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

        $request = Laracombee::addRating($this->userId, $this->itemId, (float) $rating, $options);

        $response = Laracombee::send($request)->wait();

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

        $request = Laracombee::addCartAddition($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testAddBookmark()
    {
        $options = [
            'cascadeCreate' => true,
        ];

        $request = Laracombee::addBookmark($this->userId, $this->itemId, $options);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testRecommendItemsToUser()
    {
        $filter = [];

        $response = Laracombee::recommendItemsToUser($this->userId, 1, $filter)->wait();
        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('recomms', $response);
        $this->assertArrayHasKey('recommId', $response);
    }

    public function testListUserDetailViews()
    {
        $details = Laracombee::listUserDetailViews($this->userId);

        $response = Laracombee::send($details)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $details);
        $this->assertInternalType('array', $response);
    }

    public function testListItemDetailViews()
    {
        $details = Laracombee::listItemDetailViews($this->itemId);

        $response = Laracombee::send($details)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $details);
        $this->assertInternalType('array', $response);
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

        $request = Laracombee::listItems($options);

        $response = Laracombee::send($request)->wait();

        $this->assertInternalType('array', $response);

        foreach ($response as $item) {
            $this->assertArrayHasKey('productName', $item);
            $this->assertArrayHasKey('itemId', $item);
        }
    }

    public function testGetItemPropertyInfo()
    {
        $request = Laracombee::getItemPropertyInfo('productName');

        $response = Laracombee::send($request)->wait();

        $this->assertInternalType('array', $response);
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

        $request = Laracombee::listUsers($options);

        $response = Laracombee::send($request)->wait();

        $this->assertInternalType('array', $response);
        $this->assertEquals(5, count($response));
        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);

        foreach ($response as $user) {
            $this->assertArrayHasKey('firstName', $user);
            $this->assertArrayHasKey('userId', $user);
        }
    }

    public function testListItemRatings()
    {
        $request = Laracombee::listItemRatings($this->itemId);
        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertInternalType('array', $response);
    }

    public function testListUserRatings()
    {
        $request = Laracombee::listUserRatings($this->userId);
        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertInternalType('array', $response);
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

    public function testDeleteUser()
    {
        $request = Laracombee::deleteUser($this->userId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteItem()
    {
        $request = Laracombee::deleteItem($this->itemId);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteUserProperty()
    {
        $request = Laracombee::deleteUserProperty('firstName');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteItemProperty()
    {
        $request = Laracombee::deleteItemProperty('productName');

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, $this->recombeeResponse);
    }
}
