# mpx for PHP

[![CircleCI](https://circleci.com/gh/Lullabot/mpx-php.svg?style=svg)](https://circleci.com/gh/Lullabot/mpx-php) [![Maintainability](https://api.codeclimate.com/v1/badges/cc44177e7a46c0d99d88/maintainability)](https://codeclimate.com/github/Lullabot/mpx-php/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/cc44177e7a46c0d99d88/test_coverage)](https://codeclimate.com/github/Lullabot/mpx-php/test_coverage) [![Packagist](https://img.shields.io/packagist/dt/lullabot/mpx-php.svg)](https://packagist.org/packages/lullabot/mpx-php)


## Quick Start

* PHP 7.1+
* Composer

`composer require lullabot/mpx-php`

## Example

Here is a complete example showing how to load an account and media items from
mpx. Most implementations should not contain all of this code in a single class.
Instead, create functions, classes, or services to bridge the clients, caches,
and locks into your application. For an example of how to do this, see the
[media_mpx module for Drupal 8](https://github.com/Lullabot/media_mpx).

A runnable version of this code is in a test at
`\Lullabot\Mpx\Tests\Functional\ReadmeTest::testExample()`.

```php
<?php

use Cache\Adapter\PHPArray\ArrayCachePool;
use GuzzleHttp\Psr7\Uri;
use Lullabot\Mpx\AuthenticatedClient;
use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\DataObjectFactory;
use Lullabot\Mpx\DataService\DataServiceManager;
use Lullabot\Mpx\Service\IdentityManagement\User;
use Lullabot\Mpx\Service\IdentityManagement\UserSession;
use Lullabot\Mpx\TokenCachePool;
use Symfony\Component\Lock\Store\FlockStore;

// Only required if your application is not using Composer's autoloader already.
require_once './vendor/autoload.php';

// Create a new mpx client with the default configuration.
$defaults = Client::getDefaultConfiguration();
$client = new Client(new \GuzzleHttp\Client($defaults));

// Replace your username and password here. The username must begin with `mpx/`.
$user = new User('mpx/YOU@EXAMPLE.COM', 'secret');
// The optional lock and cache storage parameters are highly recommended for
// applications with multiple threads or requests.
$session = new UserSession($user, $client);

// This registers the annotation loader.
$dataServiceManager = DataServiceManager::basicDiscovery();

$accountFactory = new DataObjectFactory($dataServiceManager->getDataService('Access Data Service', 'Account', '1.0'), $authenticatedClient);

// Replace the ID with the account ID to load.
$account = $accountFactory->load(new Uri('http://access.auth.theplatform.com/data/Account/12345'))
    ->wait();
print "The loaded account is:\n";
var_dump($account);

$mediaFactory = new DataObjectFactory($dataServiceManager->getDataService('Media Data Service', 'Media', '1.10'), $authenticatedClient);

// Replace the ID to the media item to load. You can find it under "History -> ID" in the mpx console.
$media = $mediaFactory->load(new Uri('http://data.media.theplatform.com/media/data/Media/12345'), $account)
    ->wait();
print "The loaded media is:\n";
var_dump($media);
```

## Filtering results by fields and with Q Queries

Calls to `select()` and `selectRequest()` can be filtered by exact-match fields
as well as with more complex searches.

```php
<?php

// This skips the setup from above.
$mediaFactory = new DataObjectFactory($dataServiceManager->getDataService('Media Data Service', 'Media', '1.10'), $authenticatedClient);

// Search for "cats AND dogs" in any field.
$query = new ObjectListQuery();
$cats = new Term('cats');
$termGroup = new TermGroup($cats);
$termGroup->and(new Term('dogs'));
$query->add($termGroup);

// Limit to 10 results per page.
$query->getRange()->setEndIndex(10);
$results = $mediaFactory->select($query, $account);

foreach ($results as $media) {
    var_dump($media);
}
```

## Test client

thePlatform provides a
[Media Data Service Web Client](https://data.media.theplatform.com/media/client)
that can be used for quick testing of Media Data Service APIs. Unfortunately,
the client can not be used for other data service APIs. To test those, see the
functional tests in
[tests/src/Functional](https://github.com/Lullabot/mpx-php/tree/master/tests/src/Functional)
and the configuration in
[phpunit.xml.dist](https://github.com/Lullabot/mpx-php/blob/master/phpunit.xml.dist).

## Logging

This library will log API actions that are transparent to the calling code. For
example, calling code should handle logging of invalid credentials, while this
library will log if an authentication was automatically refreshed during an
API request that resulted in a `401`.

If your application does not wish to log these actions at all, use
`\Psr\Log\NullLogger` for any constructors that require a
`\Psr\Log\LoggerInterface`.

## Implementing custom mpx fields

mpx data service objects can have up to 100 custom fields defined per account.
These fields can contain a variety of data types, with multiple namespaces of
custom fields applying to a single object. This library allows for developers
to create structured classes in their own application code that are discovered
and used automatically.

### 1. Use the console tool to create initial classes

This CLI tool uses the mpx Field API to generate matching classes. Consider
adding descriptions for each custom field in mpx, as these will be used
automatically in doc comments. Run `bin/console help mpx:create-custom-field`
for up-to-date documentation on this command.

For example, to generate classes for all custom fields attached to a Media
object:

1. Clone this repository.
1. `$ composer install`
1. `$ bin/console mpx:create-custom-field 'Php\Namespace\For\These\Classes' 'Media Data Service' 'Media' '1.10'`
1. Enter your username and password. Progress will be shown for each field that is found.

As the mpx API has no data useful in creating class names, classes for each
field namespace will be created with names like `CustomFieldClassOne.php`. It
is highly suggested that these classes are renamed to match the fields they
contain.

Each generated class will contain an `@CustomField` annotation:

```php
/**
 * @CustomField(
 *     namespace="http://access.auth.theplatform.com/data/Account/555555",
 *     service="Media Data Service",
 *     objectType="Media",
 * )
 */
```

This is what the library uses to determine which class corresponds to a given
namespace. Note that **custom fields do not have schema versions**. Be careful
when deleting or changing data types on existing fields.

These custom fields should live in your application code. As such, you will
need to provide a way to discover the classes since different applications
have different source code structures. If you're using a module for a CMS like
Drupal, it should already provide that functionality. If not, see
`\Lullabot\Mpx\DataService\CustomFieldManager::basicDiscovery` for an example
that can be adapted in many cases.

Once the classes are available, they will be automatically used when loading
mpx objects. For example, to retrieve fields for the above namespace on a
Media object, call:

```php
$dof = new DataObjectFactory($manager->getDataService('Media Data Service', 'Media', '1.10'), $this->authenticatedClient);
$media = $dof->load(new Uri('http://data.media.theplatform.com/media/data/Media/12345'))->wait();
$fields = $media->getCustomFields('http://access.auth.theplatform.com/data/Account/555555'):
```

If a custom field class is not found, a notice will be logged and the empty
`MissingCustomFieldsClass` will be attached in place of each set of fields.

## Overview of main classes

### Lullabot\Mpx\Client
mpx API implementation of [GuzzleHttp\ClientInterface](https://github.com/guzzle/guzzle/blob/master/src/ClientInterface.php).
As a Client it doesn’t do anything extra but suppress errors to force a returning HTTP 200 for XML responses.
It also handles XML from responses.

### Lullabot\Mpx\AuthenticatedClient
Manages authenticated sessions and proxies API calls with a ClientInterface implementation, automatically refreshing expired API tokens as required.

### Lullabot\Mpx\Service\IdentityManagement\UserSession
An mpx user. Just username and password getters.

### Lullabot\Mpx\Token
mpx authentication token that is returned by the platform after [sign in](https://docs.theplatform.com/help/wsf-signin-method).

### Lullabot\Mpx\TokenCachePool
Cache of user authentication tokens. This class is a wrapper around a \Psr\Cache\CacheItemPoolInterface object.

## mpx Support

This library is not supported by thePlatform. If you need help with the
library, open an issue here in GitHub. If you need help with the mpx service
itself, see the
[mpx support portal](https://theplatform.service-now.com/support_portal/) to
file a support request.

## Known issues

[#2001 in Guzzle](https://github.com/guzzle/guzzle/pull/2001) forces processing
notifications to load all objects that have been notified. Consider applying
that patch with
[composer-patches](https://github.com/cweagans/composer-patches) until a new
release is out.
