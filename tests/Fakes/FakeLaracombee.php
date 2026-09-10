<?php

namespace Amranidev\Laracombee\Tests\Fakes;

use Amranidev\Laracombee\Laracombee;
use GuzzleHttp\Promise\FulfilledPromise;
use Recombee\RecommApi\Requests\Request;

/**
 * Return deterministic API responses without constructing or calling the SDK client.
 */
class FakeLaracombee extends Laracombee
{
    /**
     * Skip SDK initialization because all API responses are supplied locally.
     */
    public function __construct()
    {
    }

    /**
     * Return a fulfilled promise containing the fake response for this request.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     * @return \GuzzleHttp\Promise\FulfilledPromise
     */
    public function send(Request $request)
    {
        return new FulfilledPromise($this->fakeResponse($request));
    }

    /**
     * Select a deterministic response for the supplied request type.
     *
     * @param \Recombee\RecommApi\Requests\Request $request
     * @return mixed
     */
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
