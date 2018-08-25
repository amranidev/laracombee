<?php

namespace Amranidev\Laracombee;

use Amranidev\Laracombee\AbstractRecombee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Laracombee extends AbstractRecombee
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add user Model.
     */
    public function addUserModel(User $user)
    {
        $this->setUserValues($user->id, $user->toArray());

        return true;
    }

    /**
     * Add Model to Items.
     */
    public function addModel(Model $model)
    {
        // @Todo
    }
}
