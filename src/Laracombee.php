<?php

namespace Amranidev\Laracombee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Laracombee extends AbstractRecombee
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUserModel(User $user)
    {
        // @todo
    }

    public function addItemModel(Model $model)
    {
        // @todo
    }
}
