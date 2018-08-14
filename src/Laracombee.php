<?php

namespace Amranidev\Laracombee;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests\AddCartAddition;
use Recombee\RecommApi\Requests\AddDetailView;
use Recombee\RecommApi\Requests\AddPurchase;
use Recombee\RecommApi\Requests\AddRating;
use Recombee\RecommApi\Requests\AddUser;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\DeleteCartAddition;
use Recombee\RecommApi\Requests\DeleteRating;
use Recombee\RecommApi\Requests\GetUserValues;
use Recombee\RecommApi\Requests\ListUsers;
use Recombee\RecommApi\Requests\MergeUsers;
use Recombee\RecommApi\Requests\RecommendItemsToUser;
use Recombee\RecommApi\Requests\SetItemValues;
use Recombee\RecommApi\Requests\DeleteItem;
use Recombee\RecommApi\Requests\DeleteUser;
class Laracombee
{
    private $client;

    private $timeout;

    public function __constructor() 
    {
        $this->client = new Client(config('laracombee.database'), config('laracombee.token'));
        $this->timeout = config('laracombee.tomeout');
    }

    public function addItem($item_id, $fields) 
    {
        $item = new SetItemValues($item_id, $fields, [
            "cascadeCreate" => true
        ]);
        
        $item->setTimeout($this->timeout);

        $this->client->send($item);
        
        //
        return true;
    }

    public function recommendItemsToUser($user_id, $limit, $filters)
    {
        $items = new RecommendItemsToUser($user_id, $limit, $filters);

        $items->setTimeout($this->timeout);

        return $this->client->send($items);
    }

    public function updateItem($item_id, $fields)
    {
        return $this->addItem($item_id, $fields);
    }

    public function removeItem($item_id)
    {
        $item = new DeleteItem($item_id);

        $item->setTimeout($this->timeout);

        $this->client->send($item);

        return true;
    }

    public function addUser($user_id) 
    {
        $user = new AddUser($user_id);

        $this->client->send($user);

        return true;
    }

    public function deleteUser($user_id)
    {
        $user = new DeleteUser($user_id);

        $this->client->send($user);

        return true;
    }

    public function mergeUsers($target_user_id, $source_user_id, $params)
    {
        $merge = new MergeUsers($target_user_id, $source_user_id, $params);

        $this->client->send($merge);

        return true;
    }
}