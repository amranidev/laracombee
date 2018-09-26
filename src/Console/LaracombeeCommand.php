<?php

namespace Amranidev\Laracombee\Console;

use Illuminate\Console\Command;
use Laracombee;

class LaracombeeCommand extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add User property.
     *
     * @param string $property.
     * @param string $type.
     *
     * @return \Recombee\RecommApi\Requests\AddUserProperty
     */
    public function addUserProperty(string $property, string $type)
    {
        return Laracombee::addUserProperty($property, $type);
    }

    /**
     * Add Item property.
     *
     * @param string $property.
     * @param string $type.
     *
     * @return \Recombee\RecommApi\Requests\AddItemProperty
     */
    public function addItemProperty(string $property, string $type)
    {
        return Laracombee::addItemProperty($property, $type);
    }

    /**
     * Delete User property.
     *
     * @param string $property.
     *
     * @return \Recombee\RecommApi\Requests\DeleteUserProperty
     */
    public function deleteUserProperty(string $property)
    {
        return Laracombee::deleteUserProperty($property);
    }

    /**
     * Delete Item property.
     *
     * @param string $property.
     *
     * @return \Recombee\RecommApi\Requests\DeleteItemProperty
     */
    public function deleteItemProperty(string $property)
    {
        return Laracombee::deleteItemProperty($property);
    }

    /**
     * Add users as bulk.
     *
     * @param array $batch.
     *
     * @return \Recombee\RecommApi\Requests\Request
     */
    public function addUsers(array $batch)
    {
        return Laracombee::addUsers($batch);
    }

    /**
     * Add items as bulk.
     *
     * @param array $batch.
     *
     * @return \Recombee\RecommApi\Requests\Request
     */
    public function addItems(array $batch)
    {
        return Laracombee::addItems($batch);
    }
}