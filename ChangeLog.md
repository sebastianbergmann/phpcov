# Changes in PHPCOV

All notable changes in PHPCOV are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [10.0.1] - 2024-12-20

### Added

* Added support for PHP 8.4

## [10.0.0] - 2024-02-02

### Added

* Added support for PHPUnit 11

### Removed

* PHPUnit 10 is no longer supported
* PHP 8.1 is no longer supported

## [9.0.2] - 2023-09-12

### Changed

* [#134](https://github.com/sebastianbergmann/phpcov/pull/134): Generate code coverage report in PHP format as first in list to avoid serializing cache data

## [9.0.1] - 2023-09-08

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [9.0.0] - 2023-02-13

### Removed

* PHP 7.3, PHP 7.4, and PHP 8.0 are no longer supported

## [8.2.1] - 2022-03-24

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [8.2.0] - 2020-10-02

### Added

* [#105](https://github.com/sebastianbergmann/phpcov/pull/105): Support for Cobertura XML report

## [8.1.2] - 2020-09-23

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [8.1.1] - 2020-09-10

### Fixed

* [#102](https://github.com/sebastianbergmann/phpcov/issues/102): Not all source files of sebastian/cli-parser packaged in PHAR

## [8.1.0] - 2020-08-13

### Added

* Added `--path-coverage` option for the `execute` command to enable branch and path coverage analysis

## [8.0.0] - 2020-08-11

### Added

* [#98](https://github.com/sebastianbergmann/phpcov/issues/98): Support for php-code-coverage ^9.1

### Changed

* Renamed `--whitelist` option to `--include`

## [7.0.2] - 2020-03-05

### Fixed

* [#95](https://github.com/sebastianbergmann/phpcov/pull/95): `TypeError` in commands

## [7.0.1] - 2020-02-09

### Fixed

* [#93](https://github.com/sebastianbergmann/phpcov/issues/93): Incorrect version info

## [7.0.0] - 2020-02-08

### Added

* Added support for PHPUnit 9

### Removed

* Removed support for PHP 7.2

## [6.0.1] - 2019-11-18

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [6.0.0] - 2019-02-20

### Added

* Added support for PHPUnit 8

### Removed

* Removed support for PHP versions older than PHP 7.2

[10.0.1]: https://github.com/sebastianbergmann/phpcov/compare/10.0.0...10.0.1
[10.0.0]: https://github.com/sebastianbergmann/phpcov/compare/9.0.2...10.0.0
[9.0.2]: https://github.com/sebastianbergmann/phpcov/compare/9.0.1...9.0.2
[9.0.1]: https://github.com/sebastianbergmann/phpcov/compare/9.0.0...9.0.1
[9.0.0]: https://github.com/sebastianbergmann/phpcov/compare/8.2.1...9.0.0
[8.2.1]: https://github.com/sebastianbergmann/phpcov/compare/8.2.0...8.2.1
[8.2.0]: https://github.com/sebastianbergmann/phpcov/compare/8.1.2...8.2.0
[8.1.2]: https://github.com/sebastianbergmann/phpcov/compare/8.1.1...8.1.2
[8.1.1]: https://github.com/sebastianbergmann/phpcov/compare/8.1.0...8.1.1
[8.1.0]: https://github.com/sebastianbergmann/phpcov/compare/8.0.0...8.1.0
[8.0.0]: https://github.com/sebastianbergmann/phpcov/compare/7.0.2...8.0.0
[7.0.2]: https://github.com/sebastianbergmann/phpcov/compare/7.0.1...7.0.2
[7.0.1]: https://github.com/sebastianbergmann/phpcov/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/sebastianbergmann/phpcov/compare/6.0.1...7.0.0
[6.0.1]: https://github.com/sebastianbergmann/phpcov/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/phpcov/compare/5.0.0...6.0.0

