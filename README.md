<p align="center">
  <img src="https://i.imgur.com/JONjk37.jpg">
</p>

<p align="center">
<a href="https://github.styleci.io/repos/144337523"><img src="https://github.styleci.io/repos/144337523/shield?branch=master" alt="StyleCI"></a> <a href="https://travis-ci.org/amranidev/laracombee"><img src="https://travis-ci.org/amranidev/laracombee.svg?branch=master" alt="StyleCI"></a> <a href="https://packagist.org/packages/amranidev/laracombee"><img src="https://poser.pugx.org/amranidev/laracombee/v/stable" alt="Version stable"></a> <a href="https://packagist.org/packages/amranidev/laracombee"><img src="https://poser.pugx.org/amranidev/laracombee/v/unstable" alt="un-Version"></a> <a href="https://packagist.org/packages/amranidev/laracombee"><img src="https://poser.pugx.org/amranidev/scaffold-interface/license" alt="un-Version"></a>
  <a href="http://laravel.com"><img src="https://img.shields.io/badge/built%20for-laravel-blue.svg" alt="Laravel"></a>
</p>

### Introduction.

Larcombee is a [Recombee](https://recombee.com) API package for Laravel, it provides a simple API implementation, to get recommendations based on the user's behaviors and interests, whatever you build using Laravel, an e-commerce, music marketplace or movies platform, a recommendation system is a must, that way you can garantee users will spend more time on your platform.

### What is Recombee?

An artificial Intelligence powered recommendation system service with an intuitive RESTful API & SDKs tailored by data scientists.

Want to know more about how it works? check this following [link](https://medium.com/recombee-blog/recommender-systems-explained-d98e8221f468).

### Getting started.

 1. Install Laracombee:
 
`composer require amranidev/laracombee`
 
 2. Add the service provider and the alias to config/app.php:
 
```php
// Service provider.
Amranidev\Laracombee\Providers\LaracombeeServiceProvider::class,

// Alias.
'Laracombee' => Amranidev\Laracombee\facades\LaracombeeFacade::class,
```

 3. Create a new instance in [recombee.com](https://www.recombee.com/)
 
 4. Add `databaseId` and `token` to `config/laracombee.php` in your project.

Congratulations, you have successfully installed Laracombee!

### Usage

With Laracombe, the integration of your data is simple, as you may know, Recombee used the user-item based database to predict recommendation based on users interests and interactions, so, you have to address which Laravel eloquent model you want to use as user as well as item.

Go to `App\User` and add `$laracombee` that you want to be migrated to Recombee db and do the same for `item`.

> Note: $laracombee property should always be static. 

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public static $laracombee = ['name', 'age'];
}
```

#### Commands.

Larcombe comes with a bunch of artisan commands that provides a fluent workflow for you, such as migrate, rollback, seed, add columns and drop columns.

##### Migration and Rollback commands.

As you remember, every time you trigger the migrate or the rollback command, Laracombee will look for `$laracombee` property and prepare the schema, you just have to specify which catalog you want to migrate/rollback (user/item) and provide the model namespace, Laracombee will do the job for you.

Migrate **`user`** : `php artisan laracombee:migrate user --class=\App\User`

Migrate **`item`** : `php artisan laracombee:migrate item --class=\App\Product`

Rollback **`user`** : `php artisan laracombee:rollback user --class=\App\User`

Rollback **`item`** : `php artisan laracombee:rollback item --class=\App\Product`

##### The Seed command.

If you want to index your users or items records that already exist in your database to recombee, you can run the seed command.

> Note: Running this command may take several minutes, depends on your records.

Index **`user`** : `php artisan laracombee:seed user --class=\App\User`

Index **`item`** : `php artisan laracombee:seed item --class=\App\Product`

##### Add/Drop columns.

You can add or drop columns with these following commands:

Add column : `php artisan laracombee:add email:string age:integer --to=user`

Drop column : `php artisan laracombee:drop email age --from=user`

### Laracombee magic methods.

The package allows to manage recombee users/items through magic methods.

#### Example.

```php
// Add a user to recombee.

$user = User::findOrFail($id);

$addUser = Laracombee::addUser($user);

Laracombee::send($addUser)->then(function () {
  // Success.
})->otherWise(function ($error) {
  //Handle Exeption.
})->wait();
```

As you can see, we've used the magic method called `addUser`, which returns `\Recombee\RecommApi\Requests\Request`, then we triggered the `send` method to save the user in recombee database, let me tell you a little bit about the `send` method.

This method is the one responsible for sending a request to Recombee server, whenever you want your app to interact with Recombee, you probably need to trigger this method, adding a new user, adding an item, or even deleting it.

The send method always returns a promise, so it becomes easy for you to manage the response from the server.

You can also add multiple users as bulk to the Recombee database, if you want to, you can use `addUsers` magic method and pass an array of users through the parameter, then you must trigger `batch` method to send the request, using `batch` could be better when sending multiple requests at the same time.

```php
// Get users from your db.
$user1 = User::findOrFail(1);
$user2 = User::findOrFail(2);
$user3 = User::findOrFail(3);

// Prepare request.
$users = Laracombee::addUsers([$user1, $user2, $user3]);

// Send request as bulk.
Laracombee::batch($users);
```

#### Other magic methods.

Update user, `Laracombee::updateUser($user);`

Merge users, `Laracombee::mergeUsers($user1, $user2);`

Add an Item, `Laracombee::addItem($item);`

Update an item, `Laracombee::updateItem($item);`

Add multiple items, `Laracombee::addItems($items);`

### API

Laracombee follows the same naming conventions as recombee, please check recombee api [docs](https://docs.recombee.com/api.html).

#### Users

- Deletes a user of given userId from the database, `Laracombee::deleteUser($user_id);`

- Merge two users, `Laracombee::mergeUsersWithId($source_user_id, $target_user_id);`

Merges interactions (purchases, ratings, bookmarks, detail views ...) of two different users under a single user ID. This is especially useful for online e-commerce applications working with anonymous users identified by unique tokens such as the session ID. In such applications, it may often happen that a user owns a persistent account, yet accesses the system anonymously while, e.g., putting items into a shopping cart. At some point in time, such as when the user wishes to confirm the purchase, (s)he logs into the system using his/her username and password. The interactions made under anonymous session ID then become connected with the persistent account, and merging these two together becomes desirable.

Merging happens between two users referred to as the target and the source. After the merge, all the interactions of the source user are attributed to the target user, and the source user is deleted.

- Gets a list of IDs of users currently present in the catalog, `Laracombee::listUsers($options);`.

- Adding an user property is somehow equivalent to adding a column to the table of users. The users may be characterized by various properties of different types, `Laracombee::addUserProperty($property, $type);`.

- Deleting an user property is roughly equivalent to removing a column from the table of users, `Laracombee::deleteUserProperty($property);`.

- Set/update (some) property values of a given user. The properties (columns) must be previously created by Add user property, `Laracombee::setUserValues($user_id, $fields);`.

- Get all the current property values of a given user, `Laracombee::getUserValues($user_id);`.
