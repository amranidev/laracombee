<?php

namespace Amranidev\Laracombee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Laracombee extends AbstractRecombee
{
    /**
     * Create new laracombee instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a user to recombee db.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return \Amranidev\Laracombee\AbstractRecombee
     */
    public function addUserModel(User $user)
    {
        $laracombeeProperties = $user::$laracombee;

        $values = collect($user->toArray())->filter(function ($value, $key) use ($laracombeeProperties) {
            return isset($laracombeeProperties[$key]);
        })->all();

        return Laracombee::setUserValues($user->id, $values);
    }

    /**
     * Add an item to recombee db.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     *
     * @return \Amranidev\Laracombee\AbstractRecombee
     */
    public function addItemModel(Model $item)
    {
        $laracombeeProperties = $item::$laracombee;

        $values = collect($item->toArray())->filter(function ($value, $key) use ($laracombeeProperties) {
            return isset($laracombeeProperties[$key]);
        })->all();

        return Laracombee::setItemValues($item->id, $values);
    }
}
