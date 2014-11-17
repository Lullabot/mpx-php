

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
$client = new Mpx\Client([], $logger);
$account = new \Mpx\Account('mpx/user@example.com', 'password', $client, $logger);
$token = $account->getToken();

// Container usage:
$container = new Pimple\Container();
$container['logger'] = function ($c) {
  return new NullLogger();
};
$container['client'] = $container->factory(function ($c) {
  return new \Mpx\Client([], $c['logger']);
});
$account = \Mpx\Account::create('mpx/user@example.com', 'password'', $container);
$token = $account->getToken();
```
