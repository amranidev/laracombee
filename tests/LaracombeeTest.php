<?php

namespace Amranidev\Laracombee\Tests;

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

        $response = Laracombee::send($request);

        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUserProperty()
    {
        $request = Laracombee::addUserProperty('firstName', 'string');

        $response = Laracombee::send($request);

        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testAddUser()
    {
        $userProperties = ['firstName' => 'Jhon Doe'];

        $request = Laracombee::setUserValues($this->userId, $userProperties);

        $response = Laracombee::send($request);

        $this->assertEquals($response, 'ok');
    }

    public function testAddItem()
    {
        $itemProperties = ['productName' => 'My product'];

        $request = Laracombee::setItemValues($this->itemId, $itemProperties);

        $response = Laracombee::send($request);

        $this->assertEquals($response, 'ok');
    }

    public function testDeleteUser()
    {
        $request = Laracombee::deleteUser($this->userId);

        $response = Laracombee::send($request);

        $this->assertEquals($response, $this->recombeeResponse);
    }

    public function testDeleteItem()
    {
        $request = Laracombee::deleteItem($this->itemId);

        $response = Laracombee::send($request);

        $this->assertEquals($response, $this->recombeeResponse);
    }
}
