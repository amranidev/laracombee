<?php

namespace Amranidev\Laracombee\Tests;

use Carbon\Carbon;
use Laracombee;

class LaracombeeTest extends TestCase
{
    public $recombeeResponse = '"ok"';

    public $userId = 1;

    public $itemId = 1;

    public function setUp()
    {
        parent::setUp();
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

    public function testAddItem()
    {
        $itemProperties = ['productName' => 'My product'];

        $request = Laracombee::setItemValues($this->itemId, $itemProperties);

        $response = Laracombee::send($request)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $request);
        $this->assertEquals($response, 'ok');
    }

    public function testAddDetailedView()
    {
        $options = ['timestamp' => Carbon::now()->toIso8601String(), 'duration' => 15, 'cascadeCreate' => true];

        $request = Laracombee::addDetailedView($this->userId, $this->itemId, $options);

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
            'filter' => '',
            'count' => 5,
            'offset' => 0,
            'returnProperties' => true,
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

    public function testListUsers()
    {
        $options = [
            'filter' => '',
            'count' => 5,
            'offset' => 0,
            'returnProperties' => true,
            'includedProperties' => ['firstName'],
        ];

        $request = Laracombee::listUsers($options);

        $response = Laracombee::send($request)->wait();

        $this->assertInternalType('array', $response);
        $this->assertEquals(5, count($response));

        foreach ($response as $user) {
            $this->assertArrayHasKey('firstName', $user);
            $this->assertArrayHasKey('userId', $user);
        }
    }

    public function testDeleteDetailView()
    {
        $detailView = Laracombee::deleteDetailView($this->userId, $this->itemId, []);

        $response = Laracombee::send($detailView)->wait();

        $this->assertInstanceOf(\Recombee\RecommApi\Requests\Request::class, $detailView);
        $this->assertEquals($response, $this->recombeeResponse);
    }

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
