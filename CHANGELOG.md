# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Add a method to yield pages in lists #107

### Changed

- Fix nextList returning true for single-item lists #108
- Fix broken range calculations for the end of a list of ranges #113
- `\Lullabot\Mpx\Service\IdentityManagement\UserInterface` has been added to
  simplify bridging user configuration from applications. #116
- Fix using the number of entries in the current page instead of the total #118

### Removed

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
