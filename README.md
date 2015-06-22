

## Requirements

* PHP 5.4+
* [Composer](http://getcomposer.org/)

## Installation
In your composer.json, add the following require directive:
```json
{
    "require" : {
        "lullabot/mpx" : "master-dev"
    }
}
```

Then run:
```bash
$ composer update
```

## Simple Usage

```php
require 'vendor/autoload.php';

$user = new Mpx\User('mpx/user@example.com', 'password');
$token = $user->acquireToken();
```

## Container Usage

```php
require 'vendor/autoload.php';

$container = new Pimple\Container();
$container['logger'] = function ($c) {
  return new GuzzleHttp\Subscriber\Log\SimpleLogger();
};
$container['client'] = function ($c) {
  $client = new Mpx\Client();
  $subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($c['logger']);
  $client->getEmitter()->attach($subscriber);
  return $client;
};
$container['cache'] = function ($c) {
  $driver = new Stash\Driver\Sqlite();
  // Calling setOptions() is required until https://github.com/tedious/Stash/pull/234 is fixed.
  $driver->setOptions();
  $pool = new Stash\Pool($driver);
  $pool->setLogger($c['logger']);
  $pool->setNamespace('mpx');
  return $pool;
}

$user = Mpx\User::create('mpx/user@example.com', 'password'', $container);
$token = $user->acquireToken();
```
