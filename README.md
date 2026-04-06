<p align="center">
  <img src="https://i.imgur.com/JONjk37.jpg" alt="Laracombee">
</p>

<p align="center">
  <a href="https://github.styleci.io/repos/144337523">
    <img src="https://github.styleci.io/repos/144337523/shield?branch=master" alt="StyleCI">
  </a>
  <a href="https://github.com/amranidev/laracombee/actions/workflows/tests.yml">
    <img src="https://github.com/amranidev/laracombee/actions/workflows/tests.yml/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/amranidev/laracombee">
    <img src="https://poser.pugx.org/amranidev/laracombee/v/stable" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/amranidev/laracombee">
    <img src="https://poser.pugx.org/amranidev/laracombee/license" alt="License">
  </a>
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/built%20for-laravel-blue.svg" alt="Laravel">
  </a>
</p>

# Laracombee

Laracombee is a Laravel package for the [Recombee](https://www.recombee.com/) recommendation API. It provides a simple Laravel-friendly interface for working with users, items, user-item interactions, and recommendations.

This package is useful for applications such as e-commerce platforms, media libraries, and marketplaces where personalized recommendations can improve engagement and retention.

## What is Recombee?

Recombee is an AI-powered recommendation service with a REST API and SDKs for building personalized recommendation systems.

To learn more about how Recombee works, see the official documentation:

- [Recombee API documentation](https://docs.recombee.com/api.html)

## Requirements

- PHP 8.2 or later
- Laravel 10, 11, or 12

## Installation

Install the package with Composer:

```bash
composer require amranidev/laracombee
```

Laravel package discovery will register the service provider automatically.

If you want to publish the configuration file, run:

```bash
php artisan vendor:publish --tag=laracombee-config
```

Then add your Recombee database ID and private token to `config/laracombee.php`.

## Configuration

Set your default user and item models in `config/laracombee.php`:

```php
'user' => \App\Models\User::class,
'item' => \App\Models\Book::class,
```

These classes are used by the package commands.

You can also configure:

- `protocol`: the HTTP protocol used for requests. The default is `http`.
- `timeout`: the default request timeout in milliseconds. The default is `2000`.
- `region`: the Recombee region. The default is `eu-west`.

## Defining Recombee Properties

Each model that should be synchronized with Recombee must define a static `$laracombee` property.

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public static $laracombee = [
        'name' => 'string',
        'age' => 'int',
    ];
}
```

Do the same for your item model.

## Usage

### Add a user

```php
use App\Models\User;
use Laracombee;

$user = User::findOrFail($id);

$request = Laracombee::addUser($user);

Laracombee::send($request)
    ->then(function () {
        // Success.
    })
    ->otherwise(function ($error) {
        // Handle the error.
    })
    ->wait();
```

### Add multiple users in a batch

```php
use App\Models\User;
use Laracombee;

$users = User::findMany([1, 2, 3])->all();

$batch = Laracombee::addUsers($users);

Laracombee::batch($batch)->wait();
```

### Recommend items to a user

```php
use App\Models\User;
use Laracombee;

$user = User::findOrFail($id);

$recommendations = Laracombee::recommendTo($user, 10)->wait();

$itemIds = collect($recommendations['recomms'])
    ->pluck('id')
    ->all();
```

Unlike most other methods in the package, recommendation methods trigger the request immediately and return a promise directly.

## Commands

Laracombee includes several Artisan commands for managing your Recombee schema and data.

### Migrate properties

```bash
php artisan laracombee:migrate user
php artisan laracombee:migrate item
```

These commands read the static `$laracombee` property from the configured model and create the corresponding Recombee properties.

### Roll back properties

```bash
php artisan laracombee:rollback user
php artisan laracombee:rollback item
```

### Seed existing records

```bash
php artisan laracombee:seed user
php artisan laracombee:seed item
```

You can also set a custom chunk size:

```bash
php artisan laracombee:seed user --chunk=250
```

### Add or drop properties manually

```bash
php artisan laracombee:add email:string age:int --to=user
php artisan laracombee:drop email age --from=user
```

### Reset the Recombee database

```bash
php artisan laracombee:reset
```

This command permanently removes all Recombee data, including users, items, properties, ratings, detail views, purchases, bookmarks, and series data. Do not run it in production unless you explicitly intend to wipe the Recombee database.

### Generate a custom Laracombee class

```bash
php artisan laracombee:new CustomLaracombee
```

This creates a new class in `app/Laracombee`.

## Available Methods

Laracombee generally follows Recombee naming conventions.

### Users

- `Laracombee::deleteUser($userId)`
- `Laracombee::mergeUsersWithId($targetUserId, $sourceUserId)`
- `Laracombee::listUsers($options = [])`
- `Laracombee::addUserProperty($property, $type)`
- `Laracombee::deleteUserProperty($property)`
- `Laracombee::setUserValues($userId, $fields)`
- `Laracombee::getUserValues($userId)`

### Items

- `Laracombee::deleteItem($itemId)`
- `Laracombee::listItems($options = [])`
- `Laracombee::addItemProperty($property, $type)`
- `Laracombee::deleteItemProperty($property)`
- `Laracombee::setItemValues($itemId, $fields)`
- `Laracombee::getItemValues($itemId)`
- `Laracombee::getItemPropertyInfo($propertyName)`

### User-item interactions

- `Laracombee::addDetailView($userId, $itemId, $options = [])`
- `Laracombee::deleteDetailView($userId, $itemId, $options = [])`
- `Laracombee::listItemDetailViews($itemId)`
- `Laracombee::listUserDetailViews($userId)`
- `Laracombee::addPurchase($userId, $itemId, $options = [])`
- `Laracombee::deletePurchase($userId, $itemId, $options = [])`
- `Laracombee::addRating($userId, $itemId, $rating, $options = [])`
- `Laracombee::deleteRating($userId, $itemId, $options = [])`
- `Laracombee::listItemRatings($itemId)`
- `Laracombee::listUserRatings($userId)`
- `Laracombee::addCartAddition($userId, $itemId, $options = [])`
- `Laracombee::deleteCartAddition($userId, $itemId, $options = [])`
- `Laracombee::addBookmark($userId, $itemId, $options = [])`
- `Laracombee::deleteBookmark($userId, $itemId, $options = [])`
- `Laracombee::setViewPortion($userId, $itemId, $portion, $options = [])`
- `Laracombee::deleteViewPortion($userId, $itemId, $options = [])`
- `Laracombee::listItemViewPortions($itemId)`
- `Laracombee::listUserViewPortions($userId)`

### Recommendations

- `Laracombee::recommendItemsToUser($userId, $limit, $options = [])`
- `Laracombee::recommendUsersToUser($userId, $limit, $options = [])`
- `Laracombee::recommendTo($user, $limit = 10, $options = [])`

### Series

- `Laracombee::addSeries($seriesId)`
- `Laracombee::insertToSeries($seriesId, $itemType, $itemId, $time)`
- `Laracombee::removeFromSeries($seriesId, $itemType, $itemId, $time)`
- `Laracombee::deleteSeries($seriesId)`
- `Laracombee::listSeries()`
- `Laracombee::listSeriesItems($seriesId)`

### Utility

- `Laracombee::batch($requests)`
- `Laracombee::send($request)`
- `Laracombee::resetDatabase()`

## Extending the Package

If you want to tailor the behavior of the package, you can extend `AbstractRecombee` and implement your own `send()` method.

```php
<?php

namespace Acme\MyRecombee;

use Amranidev\Laracombee\AbstractRecombee;
use Recombee\RecommApi\Requests\Request;

class MyRecombee extends AbstractRecombee
{
    public function __construct()
    {
        parent::__construct(
            config('laracombee.database'),
            config('laracombee.token'),
            [
                'timeout' => config('laracombee.timeout'),
                'region' => config('laracombee.region'),
                'protocol' => config('laracombee.protocol'),
            ]
        );
    }

    public function send(Request $request)
    {
        return $this->client->send($request);
    }
}
```

This is useful if you want to customize error handling, request flow, or database targeting.

## Multiple Recombee Databases

You can create additional Laracombee-style classes if you need to work with multiple Recombee databases from the same application.

The built-in generator can help you scaffold those classes:

```bash
php artisan laracombee:new ReportingLaracombee
```

## Contributing

Thank you for considering contributing to the project. Please read the [contribution guide](CONTRIBUTING.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
