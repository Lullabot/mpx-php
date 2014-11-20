

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

## Usage

```php
require 'vendor/autoload.php';

// Native usage:
$logger = new GuzzleHttp\Subscriber\Log\SimpleLogger();
$client = new GuzzleHttp\Client();
$subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($logger);
$client->getEmitter()->attach($subscriber);

$user = new Mpx\User('mpx/user@example.com', 'password', $client, $logger);
$token = $user->getValidToken();

// Container usage:
$container = new Pimple\Container();
$container['logger'] = function ($c) {
  return new GuzzleHttp\Subscriber\Log\SimpleLogger();
};
$container['client'] = $container->factory(function ($c) {
  $client = new GuzzleHttp\Client();
  $subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($c['logger']);
  $client->getEmitter()->attach($subscriber);
  return $client;
});

$user = Mpx\User::create('mpx/user@example.com', 'password'', $container);
$token = $user->getValidToken();
```
