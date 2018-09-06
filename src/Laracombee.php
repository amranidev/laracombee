<?php

namespace Amranidev\Laracombee;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;

class Laracombee extends AbstractRecombee
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUserModel(User $user) {
        // @todo
    }

    public function addItemModel(Model $model)
    {
        // @todo
    }
}
