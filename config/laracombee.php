<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Id and token.
    |--------------------------------------------------------------------------
    |
    | Here where you can define your database ID and recombee token.
    |
    */

    'database' => '',
    'token'    => '',

    'timeout'  => 2000,

    /*
    |--------------------------------------------------------------------------
    | User properties.
    |--------------------------------------------------------------------------
    |
    | Here where you can define the item properties in the recombee database.
    |
    | Value type of the user property to be created. One of:
    | int, double, string, boolean, timestamp, set, image or imageList.
    |
    */

    'user-properties' => [
        'name' => 'string',
        'age'  => 'boolean',
    ],

    /*
    |--------------------------------------------------------------------------
    | Item properties.
    |--------------------------------------------------------------------------
    |
    | Here where you can define the item properties in the recombee database.
    |
    | Value type of the item property to be created. One of:
    | int, double, string, boolean, timestamp, set, image or imageList.
    |
    */

    'item-properties' => [
        'item-name' => 'string',
        'price'     => 'int',
    ],
];
