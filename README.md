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
- Laravel 10, 11, 12, or 13 (Laravel 13 requires PHP 8.3 or later)

## Installation

Install the package with Composer:

```bash
composer require amranidev/laracombee
```

Laravel package discovery registers the service provider and the `Laracombee` facade alias automatically. You can also import the facade explicitly with `use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;`.

If you want to publish the configuration file, run:

```bash
php artisan vendor:publish --tag=laracombee-config
```

Set your credentials in `.env`:

```dotenv
RECOMBEE_DATABASE=your-database-id
RECOMBEE_TOKEN=your-private-token
```

The default configuration reads these variables. If you previously published the configuration, update its `database` and `token` entries to use `env('RECOMBEE_DATABASE', '')` and `env('RECOMBEE_TOKEN', '')`, or continue supplying those values through your existing configuration.

## Configuration

Set your default user and item models in `config/laracombee.php`:

```php
'user' => \App\Models\User::class,
'item' => \App\Models\Book::class,
```

These classes are used by the package commands.

You can also configure:

- `protocol`: the HTTP protocol used for requests. The default is `https`.
- `timeout`: the default request timeout in milliseconds. The default is `2000`.
- `region`: the Recombee region. The default is `eu-west`.

## Defining Recombee Properties

Each model that should be synchronized with Recombee must define a public static `$laracombee` property mapping property names to Recombee type strings.

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

Do the same for your item model. The default mapper uses the Eloquent primary key (`getKey()`), so custom primary key names are supported. Models must have an identifier before synchronization.

Only declared properties present in `toArray()` are exported. Eloquent hidden attributes remain excluded. User models must implement Laravel's `Authenticatable` contract; the built-in commands require Eloquent models.

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
        // $error is the original exception.
        report($error);
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

Most methods build a Recombee request, which you pass to `send()`. Recommendation methods and `batch()` return a promise directly. Call `wait()` to execute the SDK request and obtain its result.

The promise defers a synchronous SDK call; it does not provide concurrent network requests. A failed `wait()` throws the original exception. If you attach `otherwise()`, its callback receives that exception and can handle or rethrow it.

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

Seeding reads records in database chunks instead of loading the entire table. `--chunk` must be a positive integer. The `type`, `--to`, and `--from` arguments accept only `user` or `item`.

Commands return exit code `0` on success and `1` on validation or execution failure. Seeding stops at the first failed batch; previously completed batches are not rolled back.

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

This creates a subclass of the package client in your application’s `Laracombee` directory (normally `app/Laracombee`). The generator supports nested namespaces, rejects invalid class names, and refuses to overwrite existing files.

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

Resolve the configured singleton through the container or inject it into a controller or service:

```php
use Amranidev\Laracombee\Laracombee;

$client = app(Laracombee::class);
```

The service provider uses `LaracombeeConnector` to construct the SDK client and injects it into `Laracombee`. You can supply an SDK client directly for testing or custom construction:

```php
use Amranidev\Laracombee\Laracombee;
use Recombee\RecommApi\Client;

$configuration = config('laracombee');
$sdk = new Client($configuration['database'], $configuration['token'], [
    'region' => $configuration['region'],
    'protocol' => $configuration['protocol'],
]);

$client = new Laracombee($sdk, configuration: $configuration);
```

Existing `new Laracombee()` calls still use the Laravel configuration. Existing `AbstractRecombee` subclasses can still use the three-argument constructor; an optional fourth SDK client argument is available for injection.

### Custom model mapping

Extend `ModelMapper` to control identifiers or exported properties, then bind it in your application's service provider before resolving the client:

```php
use Amranidev\Laracombee\ModelMapper;

class CatalogMapper extends ModelMapper
{
    public function values(object $model): array
    {
        $values = parent::values($model);
        if (isset($values['name'])) {
            $values['name'] = trim($values['name']);
        }

        return $values;
    }
}

$this->app->bind(ModelMapper::class, CatalogMapper::class);
```

You can override `identifier(object $model): string|int`, `values(object $model): array`, and `properties(string $model): array`. Commands use `properties()` for schema operations; `identifier()` and `values()` control synchronization requests.

## Multiple Recombee Databases

Create clients from separate configuration arrays without copying the request or promise implementation:

```php
use Amranidev\Laracombee\LaracombeeConnector;

$reporting = app(LaracombeeConnector::class)->connect([
    'database' => config('services.recombee_reporting.database'),
    'token' => config('services.recombee_reporting.token'),
    'region' => 'eu-west',
    'protocol' => 'https',
    'timeout' => 2000,
]);
```

You can also run `php artisan laracombee:new ReportingLaracombee` to generate a subclass when you need custom behavior.

## Testing

```bash
composer install
vendor/bin/phpunit --display-all-issues
```

The checked-in lockfile currently targets PHP 8.4 or later for the development tools. On PHP 8.3, use `composer update` to resolve compatible tools. For Laravel 12 on PHP 8.2, use `composer update --with 'orchestra/testbench:^10.0'`.

Tests mock the SDK boundary and do not contact Recombee. They cover request construction, deferred execution and failures, model mapping, service registration, command validation, SQLite database chunking, and generated clients. The database tests require `pdo_sqlite`.

## Upgrade Notes

- Error callbacks now receive the original exception instead of its message string. Use `$error->getMessage()` when you need text.
- The default protocol is now HTTPS. Previously published configuration files keep their existing values until you update them.
- Commands report invalid arguments and SDK failures with a nonzero exit code.
- Previously generated client classes are not rewritten automatically. New generated classes extend `Laracombee` and inherit its fixes.

## Contributing

Thank you for considering contributing to the project. Please read the [contribution guide](CONTRIBUTING.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
