<p align="center">
  <img src="https://i.imgur.com/JONjk37.jpg">
</p>

<p align="center">
<a href="https://github.styleci.io/repos/144337523"><img src="https://github.styleci.io/repos/144337523/shield?branch=master" alt="StyleCI"></a> <a href="https://travis-ci.org/amranidev/laracombee"><img src="https://travis-ci.org/amranidev/laracombee.svg?branch=master" alt="StyleCI"></a>
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
