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
 
Congratulations, you have successfully installed Laracombee!
