# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2022-08-30

### Changed

- Fixed tests
- Rename master branch to main
- Alias version of cache/cache

## [1.1.0] - 2022-03-09

### Added

- New method
  \Lullabot\Mpx\DataService\Media\Media::getNormalizedDefaultThumbnailUrl
  that includes known substitutions against the defaultThumbnailUrl.

## [1.0.6] - 2022-03-03

### Changed

- Removed tests against PHP 7.3 in CircleCI config.
- Bump PHP version requirement to PHP 7.4.

## [1.0.5] - 2021-07-21

### Changed

- Updated Symfony dependencies to allow for ^4.4.
- Updated \Lullabot\Mpx\DataService\NotificationTypeExtractor::getTypes to have
  a nullable array typed return.

## [1.0.4] - 2021-07-10

### Changed

- Changed the annotations that ended in @endcode to have some trailing text to
  avoid an issue with doctrine/annotations:1.4.0 and PHP 7.4. This allows
  lullabot/mpx-php to be used with tests passing cleanly with
  drupal/core-recommended:^8.9.

## [1.0.3] - 2021-02-11

### Added

- Added dependency on phpdocumentor/reflection-docblock, previously undocumented
  and often hidden by test dependencies or other packages requiring it.

## [1.0.2] - 2021-02-11

### Added

- Added dependency on symfony/finder, previously undocumented and often hidden
  by test dependencies or other packages requiring it.

## [1.0.1] - 2021-02-09

### Added

- Run tests on PHP 7.4
- Add .codeclimate.yml to exclude coverage for certain files

### Changed

- Fix a bug in the player URL class that in some cases would result in a FALSE
  value for autoPlay not making it into the final URL
- Fix tests
- Documentation updates
- Coding standards fixes

## [1.0.0] - 2019-07-23

This release is identical to 1.0.0-rc1

## [1.0.0-rc1] - 2019-07-15

### Removed

- Remove xml support from the client #162

## [0.11.0] - 2019-05-15

### Added

- `\Lullabot\Mpx\DataService\ObjectListIterator.php` now allows the caller to
  get the total number of mpx results, via `::getTotalResults()` method.

### Changed

- New interfaces have been added, which ObjectInterface now inherits from.
   * `\Lullabot\Mpx\DataService\OwnerIdInterface` represents objects with an
      owner.
   * `\Lullabot\Mpx\DataService\GuidInterface` represents objects with a GUID.
   * `\Lullabot\Mpx\DataService\PublicIdWithGuidInterface` represents objects
     containing both a public ID and a GUID.
- `\Lullabot\Mpx\Service\Player\Url::__construct()` now typehints to a
  `PublicIdWithGuidInterface`, which is required for rendering URLs with a
  GUID.
- `\Lullabot\Mpx\Service\IdentityManagement\UserSession` no longer requires a
  lock and cache backend in the constructor. It's highly recommended to include
  both for applications that have multiple threads or requests running
  concurrently.

## [0.10.0] - 2019-01-13

### Added

- `Notification` now has an `isSyncResponse()` method to determine if no
  further notifications are available. #154

### Changed

- Setting an account context for requests is now done by setting the account
  parameter in the `AuthenticatedClient` constructor. The account parameter has
  been removed from all methods in `DataObjectFactory`. #154
- `NotificationListener` now supports sets an account context to limit
  notifications to a single account only. #154

## [0.9.0] - 2018-12-12

### Added

- Allow any object with a public ID to be used for player URLs #152
- Player URIs can now be generated using media reference GUIDs instead of
  public IDs by calling `withMediaByGuid`, and back to public IDs by calling
  `withMediaByPublicId`. #153

### Changed

- `\Lullabot\Mpx\Service\Player\Url` now is immutable, following what
  `\Psr\Http\Message\UriInterface` does. All `set` methods are now `with`
  methods. #153
- The `media` parameter on the Url constructor is now an ObjectInterface and
  not just a PublicIdentifierInterface to support guid URLs. #153

## [0.8.3] - 2018-11-05

### Added

- Embeddable player URLs can now be generated with `setEmbed` #151

## [0.8.2] - 2018-11-01

### Changed

- Update player Url class to correctly handle playAll and autoPlay query string
  params.

## [0.8.1] - 2018-10-29

### Added

- Support limiting the fields returned when selecting objects #147

## [0.8.0] - 2018-10-26

### Added

- Support calculating the availability of a media object. #146

### Changed

- Update ObjectInterface to include more common functions #145

## [0.7.0] - 2018-10-09

### Added

- The FeedConfig object is now supported. #110
- New classes have been added to support feed URL generation. See
  `\Lullabot\Mpx\Service\Feeds` for details. #111

### Changed

- The `\Lullabot\Mpx\Player\Url` has been moved to
  `\Lullabot\Mpx\Service\Player\Url`. #111
- `\Lullabot\Mpx\Player\Url` generated URLs are no longer missing `media`. #141

## [0.6.2] - 2018-07-17

### Added

- Methods that load data from mpx now support passing in HTTP client options to
  Guzzle. This is primarily useful for implementations wishing to pass custom
  headers in the request, such as `Cache-Control: no-cache` to intermediate
  proxies. #138

## [0.6.1] - 2018-07-05
## [0.6.0] - 2018-07-05

### Changed

- The library has been relicensed to MIT #136.
- `\Lullabot\Mpx\DataService\ObjectInterface::getCustomFields()` now returns
  an array of all custom field classes and does not take a namespace
  parameter. #135
- A custom field class is loaded in objects even if the response data does not
  contain any fields in that namespace. #135
- Fix not using the adapter's cache item #137

## [0.5.0] - 2018-06-21

### Added

- [#131] Link and Image data types from mpx are now supported. These are
  typically used in custom fields only.
- New date classes have been added to handle empty date fields. See the
  `\Lullabot\Mpx\DataService\DateTime` namespace for details.

### Changed

- PHP 7.1 is now the minimum required PHP version.
- All scalar properties in mpx data services classes are now nullable.
- All array properties in mpx data services default to an empty array.
- All date and time properties now return a
  `\Lullabot\Mpx\DataService\DateTime\DateTimeFormatInterface` instead of a
  `\DateTime` object.
- It is highly recommended to re-create custom fields classes from the console
  tool. This will ensure they do not cause unnecessary `\TypeError` exceptions
  to be thrown.
- Duration is now generated as a float instead of an integer.
- All traits for mpx data service objects have been removed to simplify updates
  using the console tool. This includes `AdPolicyDataTrait` and
  `PublicIdentifierTrait`.
- Rename IdInterface methods to prevent name and type conflicts #104

## [0.4.0] - 2018-06-08

### Added

- Usernames are now validated to have a leading directory component, such as
  `mpx/` #124
- Made `$objectListQuery` optional in `select()` and `selectRequest()`.
- `select()` now supports "Q Queries", which are slower but more expressive
  filters for mpx requests #129
- `\Lullabot\Mpx\DataService\QueryPartsInterface` has been added to allow other
  code to provide query parameters for select requests #129

### Changed

- The `description` field on mpx exceptions is now optional, as it is only
  included in client errors and not server errors #124
- `\Lullabot\Mpx\DataService\ByFields` has been split into two classes.
  `ByFields` now exclusively deals with `by<Field>` parameters. A new
  `\Lullabot\Mpx\DataService\ObjectListQuery` class handles ranges, sorts,
  and filters. #129
- `select()` and `selectRequest()` in DataObjectFactory now take a
  `ObjectListQuery` parameter instead of a `ByFields` parameter #129

## [0.3.0] - 2018-05-28

### Added

- Add `\Lullabot\Mpx\AuthenticatedClient::setTokenDuration` to set a limit for
  token lifetimes #125
- Add a method / interface for fetching the raw JSON response #126

## [0.2.0] - 2018-05-18

### Added

- Service URLs are now cached when loading objects and listening for
  notifications. #114
- Support for loading custom fields, including denormalizing into custom
  classes not shipped with this library. See the README for detailed
  documentation. #120
- Add a discovery interface for Custom Fields. #122

### Changed

- Fix broken range calculations for the end of a list of ranges #113
- `\Lullabot\Mpx\Service\IdentityManagement\UserInterface` has been added to
  simplify bridging user configuration from applications. #116
- `\Lullabot\Mpx\Service\AccessManagement\ResolveAllUrls` now follows what
  `\Lullabot\Mpx\Service\AccessManagement\ResolveDomain` does and has changed
  it's API as a result. #144
- The UnixMicrosecondNormalizer class has been renamed to
  UnixMillisecondNormalizer #115
- Fix using the number of entries in the current page instead of the total #118
- The deserialize method on DataObjectFactory is now protected #120
- create() methods that were broken and unused were removed in the plugin
  managers #120

## [0.1.1] - 2018-05-16

### Added

- Add a method to yield pages in lists #107

### Changed

- Fix nextList returning true for single-item lists #108

## [0.1.0] - 2018-05-04

The first alpha-quality release of mpx for PHP.

### Added

- Support for loading Accounts, Media, and Players.
- Support for querying mpx for available services.
- Automatic upgrade of load requests to HTTPS.
- Annotation API for adding support for additional Data Service objects.
- A CLI tool to help generate data service class implementations.
- Automatic renewal of expired tokens for authenticated requests.
- Interfaces for implementing libraries to use such as
  `\Lullabot\Mpx\DataService\IdInterface`.
- Automatic casting of mpx errors to exceptions.
- Support for read-only data services.
- Support for listening for notifications for any Data Service object.
- Support for querying any data service object. For example, library consumers
  can query for all Media matching a given title, sorted by a field.
- Support for transparently iterating over object-list responses from mpx.
- Stampede protection when signing in to mpx via the Symfony Lock component.
- Player `iframe` URLs can be generated for a given media asset.
- Cache support for authentication tokens and service resolution API calls.
