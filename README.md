

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
$logger = new Psr\Log\NullLogger();
$client = new GuzzleHttp\Client();
$user = new Mpx\User('mpx/user@example.com', 'password', $client, $logger);
$token = $user->getToken();

// Container usage:
$container = new Pimple\Container();
$container['logger'] = function ($c) {
  return new GuzzleHttp\Subscriber\Log\SimpleLogger();
};
$container['client'] = $container->factory(function ($c) {
  $client = new GuzzleHttp\Client();
  $formatter = new GuzzleHttp\Subscriber\Log\Formatter(GuzzleHttp\Subscriber\Log\Formatter::DEBUG);
  $subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($c['logger'], $formatter);
  $client->getEmitter()->attach($subscriber);
  return $client;
});
$user = Mpx\User::create('mpx/user@example.com', 'password'', $container);
$token = $user->getToken();
```
