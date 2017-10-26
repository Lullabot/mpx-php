

## Requirements

* PHP 5.6+
* [Composer](http://getcomposer.org/)

## Installation
```
composer require lullabot/mpx-php
```

## Usage

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
};

// Create the user.
$user = Mpx\User::create('mpx/user@example.com', 'password', $container);

// Create the media service.
$mediaService = Mpx\Object\Media::createService($user, $container);

// Create the media notification service.
$mediaNotificationService = $mediaService->getNotificationService();
$mediaNotificationService->getUri()->getQuery()->set('account', 'Import account');

// Fetch the last notification ID from the media notification service.
$mediaNotificationService->syncLatestId();

// Fetch up to 500 notifications.
$mediaNotificationService = $mediaNotifications->readNotifications();

// Wait and listen for any media notifications.
$mediaNotificationService = $mediaNotifications->readNotifications(0, ['query' => ['block' => true]]);

```
