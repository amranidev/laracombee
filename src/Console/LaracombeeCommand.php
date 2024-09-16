<?php

namespace Amranidev\Laracombee\Console;

use Amranidev\Laracombee\Facades\LaracombeeFacade;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;

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
        return LaracombeeFacade::addUserProperty($property, $type);
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
        return LaracombeeFacade::addItemProperty($property, $type);
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
        return LaracombeeFacade::deleteUserProperty($property);
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
        return LaracombeeFacade::deleteItemProperty($property);
    }

    /**
     * Add user to recombee.
     *
     * @param \Illuminate\Foundation\Auth\User $user.
     *
     * @return \Recombee\RecommApi\Requests\Request
     */
    public function addUser(User $user)
    {
        return LaracombeeFacade::addUser($user);
    }

    /**
     * Add item to recombee.
     *
     * @param \Illuminate\Database\Eloquent\Model $item.
     *
     * @return \Recombee\RecommApi\Requests\Request
     */
    public function addItem(Model $item)
    {
        return LaracombeeFacade::addItem($item);
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
        return LaracombeeFacade::addUsers($batch);
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
        return LaracombeeFacade::addItems($batch);
    }
}