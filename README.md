

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

// Create the user object.
$user = new Mpx\User('mpx/user@example.com', 'password');

// Create the media notification service.
$mediaNotifications = new Mpx\Service\Notification('https://read.data.media.theplatform.com/media/notify?account=Import%20account&filter=Media', $user);

// Fetch the last notification ID from the media notification service.
$mediaNotifications->fetchLatestId();

// Wait and listen for any media notifications.
$notifications = $mediaNotifications->listen();

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

// Create the user.
$user = Mpx\User::create('mpx/user@example.com', 'password', $container);

// Create the media notification service.
$url = GuzzleHttp\Url::fromString('https://read.data.media.theplatform.com/media/notify');
$url->getQuery()->set('account', 'Import account');
$url->getQuery()->set('filter', 'Media');
$url->getQuery()->set('clientId', 'README.md');
$mediaNotifications = Mpx\Service\Notification::create($url, $user, NULL, $container);

// Fetch the last notification ID from the media notification service.
$mediaNotifications->fetchLatestId();

// Wait and listen for any media notifications.
$notifications = $mediaNotifications->listen();

```
