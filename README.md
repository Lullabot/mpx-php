# MPX for PHP

[![CircleCI](https://circleci.com/gh/Lullabot/mpx-php.svg?style=svg)](https://circleci.com/gh/Lullabot/mpx-php) [![Maintainability](https://api.codeclimate.com/v1/badges/cc44177e7a46c0d99d88/maintainability)](https://codeclimate.com/github/Lullabot/mpx-php/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/cc44177e7a46c0d99d88/test_coverage)](https://codeclimate.com/github/Lullabot/mpx-php/test_coverage)

## Quick Start

* PHP 7.0+
* Composer

`composer require lullabot/mpx-php`

## Example

```php
<?php

use Cache\Adapter\PHPArray\ArrayCachePool;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Lullabot\Mpx\User;
use Psr\Log\NullLogger;

// Only required if your application is not using Composer's autoloader already.
require_once './vendor/autoload.php';

// Create a new MPX client with the default configuration.
$defaults = Client::getDefaultConfiguration();
$client = new Client(new GuzzleHttp\Client($defaults));

// Replace your username and password here. The username must begin with `mpx/`.
$user = new User('mpx/YOU@EXAMPLE.COM', 'secret');
$tokenCachePool = new TokenCachePool(new ArrayCachePool());
$session = new UserSession($client, $user, $tokenCachePool, new NullLogger());

// This registers the annotation loader.
$dataServiceManager = DataServiceManager::basicDiscovery();

$accountFactory = new DataObjectFactory($dataServiceManager, $session, 'Access Data Service', '/data/Account');

// Replace the ID with the account ID to load.
$account = $accountFactory->load(12345)
    ->wait();
print "The loaded account is:\n";
var_dump($account);

$mediaFactory = new DataObjectFactory($dataServiceManager, $session, 'Media Data Service', '/data/Media');

// Replace the ID to the media item to load. You can find it under "History -> ID" in the MPX console.
$media = $mediaFactory->load(12345, $account)
    ->wait();
print "The loaded media is:\n";
var_dump($media);
```

## Logging

This library will log API actions that are transparent to the calling code. For
example, calling code should handle logging of invalid credentials, while this
library will log if an authentication was automatically refreshed during an
API request that resulted in a `401`.

If your application does not wish to log these actions at all, use
`\Psr\Log\NullLogger` for any constructors that require a
`\Psr\Log\LoggerInterface`.

## Overview of main classes

### Lullabot\Mpx\Client
MPX API implementation of [GuzzleHttp\ClientInterface](https://github.com/guzzle/guzzle/blob/master/src/ClientInterface.php).
As a Client it doesn’t do anything extra but suppress errors to force a returning HTTP 200 for XML responses.
It also handles XML from responses.

### Lullabot\Mpx\Service\IdentityManagement\UserSession
Manages authenticated sessions and proxies API calls with a ClientInterface implementation, automatically refreshing expired API tokens as required.

### Lullabot\Mpx\User
An MPX user. Just username and password getters.

### Lullabot\Mpx\Token
MPX authentication token that is returned by the platform after [sign in](https://docs.theplatform.com/help/wsf-signin-method).

### Lullabot\Mpx\TokenCachePool
Cache of user authentication tokens. This class is a wrapper around a \Psr\Cache\CacheItemPoolInterface object.
