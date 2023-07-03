# Rinvex Menus Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](CONTRIBUTING.md).


## [v7.1.2] - 2023-07-03
- Update composer dependencies
- Use canonicalized absolute pathnames for resources

## [v7.1.1] - 2023-06-29
- Refactor resource loading and publishing
- Fix: use $item instead of $this (#103)

## [v7.1.0] - 2023-05-02
- Add support for Laravel v11, and drop support for Laravel v9
- Update phpunit to v10.1 from v9.5

## [v7.0.0] - 2023-01-09
- Drop PHP v8.0 support and update composer dependencies

## [v6.2.1] - 2022-12-30
- add feature to destroy menu item dropdown (#102)

## [v6.2.0] - 2022-05-17
- Simplify menu divider since it doesn't have a link, so it doesn't need a link attributes
- Rename attributes to linkAttributes to distinguish from itemAttributes
- Add support for list item attributes

## [v6.1.0] - 2022-02-14
- Update composer dependencies to Laravel v9
- Use PHP v8 nullsafe operator

## [v6.0.0] - 2021-08-22
- Drop PHP v7 support, and upgrade rinvex package dependencies to next major version
- Update composer dependencies
- Upgrade to GitHub-native Dependabot
- Use app() method alias `has` instead of `bound` for better readability

## [v5.0.2] - 2021-04-27
- Fix PHP v8.x compatibility issue

## [v5.0.1] - 2020-12-25
- Add support for PHP v8

## [v5.0.0] - 2020-12-22
- Upgrade to Laravel v8

## [v4.1.0] - 2020-06-15
- Drop PHP 7.2 & 7.3 support from travis

## [v4.0.4] - 2020-05-30
- Remove undefined $url variable
- Allow specifying menu type when calling findByTitleOrAdd
- Hide parent menu if it doesn't have any visible items
- Add menu type to all items for easier identification
- Fix hide logic for hiding parent items without visible children, only if type is dropdown or header
- Remove default indent size config
- Add support for interface based service binding

## [v4.0.3] - 2020-04-04
- Fix namespace issue

## [v4.0.2] - 2020-04-04
- Enforce consistent artisan command tag namespacing
- Enforce consistent package namespace
- Drop laravel/helpers usage as it's no longer used
- Update orchestra/testbench package (fix #21)

## [v4.0.1] - 2020-03-15
- Fix wrong package version laravelcollective/html

## [v4.0.0] - 2020-03-15
- Upgrade to Laravel v7.1.x & PHP v7.4.x

## [v3.0.3] - 2020-03-13
- Tweak TravisCI config
- Add migrations autoload option to the package
- Tweak service provider `publishesResources`
- Remove indirect composer dependency
- Drop using global helpers
- Update StyleCI config

## [v3.0.2] - 2019-11-23
- Add missing laravel/helpers composer package
- Add missing composer dependency rinvex/laravel-support

## [v3.0.1] - 2019-09-24
- Add missing laravel/helpers composer package

## [v3.0.0] - 2019-09-23
- Upgrade to Laravel v6 and update dependencies

## [v2.1.0] - 2019-06-02
- Update composer deps
- Drop PHP 7.1 travis test

## [v2.0.0] - 2019-03-03
- Require PHP 7.2 & Laravel 5.8
- Apply PHPUnit 8 updates

## [v1.0.2] - 2019-01-03
- Rename environment variable QUEUE_DRIVER to QUEUE_CONNECTION
- Fix renderView return type

## [v1.0.1] - 2018-12-22
- Update composer dependencies
- Add PHP 7.3 support to travis

## [v1.0.0] - 2018-10-01
- Enforce Consistency
- Support Laravel 5.7+
- Rename package to rinvex/laravel-menus

## [v0.0.2] - 2018-09-22
- Update travis php versions
- Drop StyleCI multi-language support (paid feature now!)
- Update composer dependencies
- Prepare and tweak testing configuration
- Update StyleCI options
- Update PHPUnit options

## v0.0.1 - 2018-02-18
- Tag first release

[v7.1.2]: https://github.com/rinvex/laravel-menus/compare/v7.1.1...v7.1.2
[v7.1.1]: https://github.com/rinvex/laravel-menus/compare/v7.1.0...v7.1.1
[v7.1.0]: https://github.com/rinvex/laravel-menus/compare/v7.0.0...v7.1.0
[v7.0.0]: https://github.com/rinvex/laravel-menus/compare/v6.2.1...v7.0.0
[v6.2.1]: https://github.com/rinvex/laravel-menus/compare/v6.2.0...v6.2.1
[v6.2.0]: https://github.com/rinvex/laravel-menus/compare/v6.1.0...v6.2.0
[v6.1.0]: https://github.com/rinvex/laravel-menus/compare/v6.0.0...v6.1.0
[v6.0.0]: https://github.com/rinvex/laravel-menus/compare/v5.0.2...v6.0.0
[v5.0.2]: https://github.com/rinvex/laravel-menus/compare/v5.0.1...v5.0.2
[v5.0.1]: https://github.com/rinvex/laravel-menus/compare/v5.0.0...v5.0.1
[v5.0.0]: https://github.com/rinvex/laravel-menus/compare/v4.1.0...v5.0.0
[v4.1.0]: https://github.com/rinvex/laravel-menus/compare/v4.0.4...v4.1.0
[v4.0.4]: https://github.com/rinvex/laravel-menus/compare/v4.0.3...v4.0.4
[v4.0.3]: https://github.com/rinvex/laravel-menus/compare/v4.0.2...v4.0.3
[v4.0.2]: https://github.com/rinvex/laravel-menus/compare/v4.0.1...v4.0.2
[v4.0.1]: https://github.com/rinvex/laravel-menus/compare/v4.0.0...v4.0.1
[v4.0.0]: https://github.com/rinvex/laravel-menus/compare/v3.0.3...v4.0.0
[v3.0.3]: https://github.com/rinvex/laravel-menus/compare/v3.0.2...v3.0.3
[v3.0.2]: https://github.com/rinvex/laravel-menus/compare/v3.0.1...v3.0.2
[v3.0.1]: https://github.com/rinvex/laravel-menus/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/rinvex/laravel-menus/compare/v2.1.0...v3.0.0
[v2.1.0]: https://github.com/rinvex/laravel-menus/compare/v2.0.0...v2.1.0
[v2.0.0]: https://github.com/rinvex/laravel-menus/compare/v1.0.2...v2.0.0
[v1.0.2]: https://github.com/rinvex/laravel-menus/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/rinvex/laravel-menus/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rinvex/laravel-menus/compare/v0.0.2...v1.0.0
[v0.0.2]: https://github.com/rinvex/laravel-menus/compare/v0.0.1...v0.0.2
