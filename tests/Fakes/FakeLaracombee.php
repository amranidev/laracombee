<?php

namespace Amranidev\Laracombee\Tests\Fakes;

use Amranidev\Laracombee\Laracombee;
use GuzzleHttp\Promise\FulfilledPromise;
use Recombee\RecommApi\Requests\Request;

class FakeLaracombee extends Laracombee
{
    public function __construct()
    {
    }

    public function send(Request $request)
    {
        return new FulfilledPromise($this->fakeResponse($request));
    }

    protected function fakeResponse(Request $request): mixed
    {
        return match (class_basename($request)) {
            'GetUserValues'        => ['userId' => 1, 'firstName' => 'Jhon Doe'],
            'GetItemValues'        => ['itemId' => 1, 'productName' => 'My product'],
            'RecommendItemsToUser' => [
                'recomms'  => [['id' => '1', 'values' => ['productName' => 'My product']]],
                'recommId' => 'test-recommendation',
            ],
            'ListUserDetailViews',
            'ListItemDetailViews',
            'ListItemRatings',
            'ListUserRatings'     => [],
            'ListItems'           => [['itemId' => '1', 'productName' => 'My product']],
            'GetItemPropertyInfo' => ['name' => 'productName', 'type' => 'string'],
            'ListUsers'           => [['userId' => '1', 'firstName' => 'Jhon Doe']],
            'ListSeries'          => [['seriesId' => 'laracombee-series']],
            'ListSeriesItems'     => [['itemId' => '1']],
            default               => 'ok',
        };
    }
}
