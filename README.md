

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
$client = new Mpx\Client();
$subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($logger);
$client->getEmitter()->attach($subscriber);
$tokenService = new Mpx\TokenStaticService($client, $logger);

$user = new Mpx\User('mpx/user@example.com', 'password', $client, $logger, $tokenService);
$token = $user->acquireToken();

// Container usage:
$container = new Pimple\Container();
$container['logger'] = function ($c) {
  return new GuzzleHttp\Subscriber\Log\SimpleLogger();
};
$container['client'] = $container->factory(function ($c) {
  $client = new Mpx\Client();
  $subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($c['logger']);
  $client->getEmitter()->attach($subscriber);
  return $client;
});
$container['token.service'] = function ($c) {
  return new Mpx\TokenMemoryService($c['client'], $c['logger']);
}

$user = Mpx\User::create('mpx/user@example.com', 'password'', $container);
$token = $user->acquireToken();
```
