<p align="center">
  <img src="https://i.imgur.com/JONjk37.jpg">
</p>

<p align="center">
<a href="https://github.styleci.io/repos/144337523"><img src="https://github.styleci.io/repos/144337523/shield?branch=master" alt="StyleCI"></a> <a href="https://travis-ci.org/amranidev/laracombee"><img src="https://travis-ci.org/amranidev/laracombee.svg?branch=master" alt="StyleCI"></a> <a href=https://scrutinizer-ci.com/g/amranidev/laracombee/badges><img src="https://scrutinizer-ci.com/g/amranidev/laracombee/badges/quality-score.png?b=master"></a> <a href="https://packagist.org/packages/amranidev/laracombee"><img src="https://poser.pugx.org/amranidev/laracombee/v/stable" alt="Version stable"></a> <a href="https://packagist.org/packages/amranidev/laracombee"><img src="https://poser.pugx.org/amranidev/scaffold-interface/license" alt="un-Version"></a>
  <a href="http://laravel.com"><img src="https://img.shields.io/badge/built%20for-laravel-blue.svg" alt="Laravel"></a>
</p>

# Introduction.

Larcombee is a [Recombee](https://recombee.com) API package for Laravel, it provides a simple API implementation, to get recommendations based on the user's behaviors and interests, whatever you build using Laravel, an e-commerce, music marketplace or movies platform, a recommendation system is a must, that way you can guarantee users will spend more time on your platform.

# What is Recombee?

An artificial Intelligence powered recommendation system service with an intuitive RESTful API & SDKs tailored by data scientists.

Want to know more about how it works? check this following [link](https://medium.com/recombee-blog/recommender-systems-explained-d98e8221f468).

# Getting started.

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

# Usage

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

    public static $laracombee = ['name' => 'string', 'age' => 'int'];
}
```

## Configuration.

First thing first, define your default **User** and **Item** class in `laracombee.php` configuration file.

```php
    /*
    |--------------------------------------------------------------------------
    | Default models for user and item.
    |--------------------------------------------------------------------------
    |
    | Here where you can define the default class for user and item.
    |
    */

    'user'  => app(\App\User::class),
    'item'  => app(\App\Book::class),
```

When you trigger a laracombee artisan command, it will automatically use those classes as a reference.

### Other configuration options.

- You can define which http `protocol` you want to use, by default its `http`
- You can define the defualt `timeout` in milliseconds for each request. by dedault its `2000`

## Commands.

Larcombe comes with a bunch of artisan commands that provides a fluent workflow for you, such as migrate, rollback, seed, add columns and drop columns.

## Migration and Rollback commands.

As you remember, every time you trigger the migrate or the rollback command, Laracombee will look for `$laracombee` property and prepare the schema, you just have to specify which catalog you want to migrate/rollback (user/item) and provide the model namespace, Laracombee will do the job for you.

Migrate **`user`** : `php artisan laracombee:migrate user`

Migrate **`item`** : `php artisan laracombee:migrate item`

Rollback **`user`** : `php artisan laracombee:rollback user`

Rollback **`item`** : `php artisan laracombee:rollback item`

### The Seed command.

If you want to index your users or items records that already exist in your database to recombee, you can run the seed command.

> Note: Running this command may take several minutes, depends on your records.

Index **`user`** : `php artisan laracombee:seed user`

Index **`item`** : `php artisan laracombee:seed item`

### Add/Drop columns.

You can add or drop columns with these following commands:

Add column : `php artisan laracombee:add email:string age:integer --to=user`

Drop column : `php artisan laracombee:drop email age --from=user`

### Reset Command.

- `php artisan laracombee:reset`

This command will completely erases all your data, including items, item properties, series, user database, purchases, ratings, detail views, and bookmarks. Make sure the request to be never executed in production environment! Resetting your database is irreversible, :warning: **Think twice before running this command** :warning:



## Laracombee magic methods.

The package allows to manage recombee users/items through magic methods.

### Example.

```php
// Add a user to recombee.

$user = User::findOrFail($id);

$addUser = Laracombee::addUser($user);

Laracombee::send($addUser)->then(function () {
  // Success.
})->otherWise(function ($error) {
  // Handle Exeption.
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

You can also recommend items to user.

```php

$user = User::findOrFail($id);

// Prepare the request for recombee server, we need 10 recommended items for a given user.
$recommendations = Laracombee::recommendTo($user, 1O);

```

**Note**: Unlike the rest of the API, recommendation API triggers the send method automatically, so there will be no need to call send method.

Recommendation API returns an array response that contains an array of recommended (items ids) called recomms.

```php

$itemsIds = $recommendations['recomms']; // [2, 5, 55, 24, 32, 91]

$items = \App\Item::find($itemsIds);

```

### Other magic methods.

Update user, `Laracombee::updateUser($user);`

Merge users, `Laracombee::mergeUsers($user1, $user2);`

Add an Item, `Laracombee::addItem($item);`

Update an item, `Laracombee::updateItem($item);`

Add multiple items, `Laracombee::addItems($items);`

# API.

Laracombee follows the same naming conventions as recombee, please check recombee api [docs](https://docs.recombee.com/api.html).

## Users.

- Deletes a user of given userId from the database, `Laracombee::deleteUser($user_id);`

- Merge two users, `Laracombee::mergeUsersWithId($source_user_id, $target_user_id);`

Merges interactions (purchases, ratings, bookmarks, detail views ...) of two different users under a single user ID. This is especially useful for online e-commerce applications working with anonymous users identified by unique tokens such as the session ID. In such applications, it may often happen that a user owns a persistent account, yet accesses the system anonymously while, e.g., putting items into a shopping cart. At some point in time, such as when the user wishes to confirm the purchase, (s)he logs into the system using his/her username and password. The interactions made under anonymous session ID then become connected with the persistent account, and merging these two together becomes desirable.

Merging happens between two users referred to as the target and the source. After the merge, all the interactions of the source user are attributed to the target user, and the source user is deleted.

- Gets a list of IDs of users currently present in the catalog, `Laracombee::listUsers($options);`.

- Adding an user property is somehow equivalent to adding a column to the table of users. The users may be characterized by various properties of different types, `Laracombee::addUserProperty($property, $type);`.

- Deleting an user property is roughly equivalent to removing a column from the table of users, `Laracombee::deleteUserProperty($property);`.

- Set/update (some) property values of a given user. The properties (columns) must be previously created by Add user property, `Laracombee::setUserValues($user_id, $fields);`.

- Get all the current property values of a given user, `Laracombee::getUserValues($user_id);`.

## Items.

- Adding an item property is somehow equivalent to adding a column to the table of items. The items may be characterized by various properties of different types, `Laracombee::addItemProperty($property, $type);`

- Deleting an item property is roughly equivalent to removing a column from the table of items, `Laracombee::deleteItemProperty($property);`

- Set/update (some) property values of a given item. The properties (columns) must be previously created by Add item property, `Laracombee::setItemValues($item_id, $fields);`

- Get all the current property values of a given item, `Laracombee::getItemValues($item_id);`

- Deletes an item of given itemId from the catalog, `Laracombee::deleteItem($item_id);`

## User-Item Interactions.

The following method allow adding, deleting and listing of interactions between the users and the items.

### Detail views

- Adds a detail view of a given item made by a given user, `Laracombee::addDetailView($user_id, $item_id, $options);`

- Deletes an existing detail view uniquely specified by (userId, itemId, and timestamp) or all the detail views with given userId and itemId if timestamp is omitted, `Laracombee::deleteDetailView($user_id, $item_id, $options);`

- List all the detail views of a given item ever made by different users, `Laracombee::listItemDetailViews($item_id);`

- Lists all the detail views of different items ever made by a given user, `Laracombee::listUserDetailViews($user_id);`

### Purchases

- Adds a purchase of a given item made by a given user, `Laracombee::dddPurchase($user_id, $item_id, $options);`

- Deletes an existing purchase uniquely specified by userId, itemId, and timestamp or all the purchases with given userId and itemId if timestamp is omitted, `Laracombee::deletePurchase($user_id, $item_id, $options);`

### Ratings

- Adds a rating of given item made by a given user, `Laracombee::addRating($user_id, $iten_id, $options);`

- Deletes an existing rating specified by (userId, itemId, timestamp) from the database or all the ratings with given userId and itemId if timestamp is omitted, `Laracombee::deleteRating($user_id, $item_id, $options);`

### Cart additions

- Adds a cart addition of a given item made by a given user, `Laracombee::addCartAddition($user_id, $item_id, $options);`

- Deletes an existing cart addition uniquely specified by userId, itemId, and timestamp or all the cart additions with given userId and itemId if timestamp is omitted, `Laracombee::deleteCartAddition($user_id, $item_id, $options);`


### Bookmarks

- Adds a bookmark of a given item made by a given user, `Laracombee::addBookmark($user_id, $item_id, $options);`

- Deletes a bookmark uniquely specified by userId, itemId, and timestamp or all the bookmarks with given userId and itemId if timestamp is omitted, `Laracombee::deleteBookmark($user_id, $item_id, $options);`


### Retrieve Recommendations

Recommendation methods are capable of recommending items (Recommend items to user, Recommend users to user).

- Based on user’s past interactions (purchases, ratings, etc.) with the items, recommends top-N items that are most likely to be of high value for a given user, `Laracombee::recommendItemsToUser($user_id, $limit, $options);`

- Get similar users as some given user, based on the user’s past interactions (purchases, ratings, etc.) and values of properties, `Laracombee::recommendUsersToUser($user_id, $limit, $options);`

# Tailor your own magic methods.

Sometimes, we need to keep our code consistent, so we wish that we can extend the installed package functionality and adapt it to our needs.

You can tailor your API however you like, you can do that by extending the `AbstractRecombee` class and implement the `send` abstract method, then you're good to go.

Example:

Let's say that you don't like the way Laracombee handle recombee requests/response with promises, dealing with exceptions the way you want, and also, you need a method that can make a user as a top seller in recombee database.

Lets create a new class, you can register it as a facade (if you would like to).

```php

<?php

namespace Acme\Myrecombee;

use Recombee\RecommApi\Requests\Request;
use Amranidev\Laracombee\AbstractRecombee;

class MyRecombee extends AbstractRecombee
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param \App\User $user
     */
    public function setTopSeller(User $user)
    {
        // Note that we assume that 'isTopSeller' column is already exists in the db. 
        return $this->setUserValues($user->id, ['isTopSeller' => true]);
    }
    
     /**
      * Send request.
      *
      * @param \Recombee\RecommApi\Requests\Request $request
      *
      * @return mixed
      */
     public function send(Request $request)
     {       
         try {
             $response = $this->client->send($request);
         } catch (Exceptions\ApiTimeoutException $e) {
              // Deal with Api exception
         } catch (Exceptions\ResponseException $e) {
              // Deal with Response exception
         } catch (Exceptions\ApiException $e) {
              // Deal with Api exception
         }

         return $response;
     }
}
```

Then you can use these functionality in your project.

```php

$user = User::findorfail($id);

$request = MyRecombee::setTopSeller($user);

$response = MyRecombee::send($request);

```

# Contributing

Thank you for considering contributing to this project! The contribution guide can be found in [Contribution guide](CONTRIBUTING.md).

Feel free to report any bugs, submit any feature request, or even ask any questions.

# Todo

- Add the remaining API to larcombe, check the recombee [api](https://docs.recombee.com/api.html) docs.
