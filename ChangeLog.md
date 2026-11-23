# Changes in PHPCOV

All notable changes in PHPCOV are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [13.3.0] - 2026-MM-DD

### Added

* `phpcov patch-coverage` no longer requires a patch file. When none is given, it uses Git to determine the lines that were added or changed in the repository that the current working directory belongs to: the commits that are not on the default branch of the `origin` remote, staged and unstaged changes, and the files that are neither tracked nor ignored. `--path-prefix` is not needed for this, and how Git is configured (an external diff tool, for instance) does not affect the result
* Added `--base` CLI option for configuring the revision that `phpcov patch-coverage` compares the working tree with when it uses Git
* When it uses Git, `phpcov patch-coverage` fails with exit code `2` when a file that was changed, and that is located in the directory that code coverage was collected for, was modified after the code coverage data was written. The `--do-not-require-up-to-date-code-coverage` CLI option disables this check
* Added `--require-matching-base-paths` CLI option for requiring that the code coverage data that is merged was collected in the same location
* Added `--do-not-require-matching-granularity` CLI option for merging code coverage data that was collected with different granularity (line coverage, or line and branch coverage, or line, branch, and path coverage)
* `phpcov merge` prints a warning when there is code coverage data for files that are not part of the reports because these files were not found. Before, this code coverage data was left out silently

### Changed

* `phpcov patch-coverage` reports consecutive changed executable lines that are not covered as a range (`src/Example.php:11-12, 20`). Before, it printed one line for each line that is not covered
* `phpcov patch-coverage` exits with exit code `0` when the patch does not change files that code coverage data is available for, or does not change executable lines. Before, a patch that does not change files that code coverage data is available for, such as a patch that only changes the documentation, was reported as a failure with exit code `2`
* `phpcov patch-coverage` exits with exit code `2` when the files that the patch changes could not be found: when the paths in the patch were resolved against a directory that does not contain these files, or that is unrelated to the location the code coverage data was collected in. The message that is printed names both directories
* `phpcov patch-coverage` fails with exit code `255` and a message that says so when the patch file is not a unified diff, which is what `git diff` writes when an external diff tool is configured, for instance. Before, it reported that it was unable to detect executable lines that were changed, and asked whether `--path-prefix` is needed
* `phpcov merge` fails when the code coverage data that is merged was not collected with the same granularity. Branch and path coverage that is merged with code coverage data that was collected without it is incomplete, and was reported as if it were complete
* The file that `phpcov merge --php` writes has the path that is configured using `--source` as the base path of the merged code coverage data. Before, `--source` was only used for the reports that are generated from the merged code coverage data, and the file had the base path of the first `.cov` file that was merged
* `phpcov merge --php` uses phpunit/php-code-coverage to write the file with the merged code coverage data instead of an implementation of its own

### Fixed

* `phpcov patch-coverage` does not find the changed lines in code coverage data that has the root directory as its base path
* `phpcov patch-coverage` does not find the changed lines when `--path-prefix` is not used. The paths in the patch are now resolved against the current working directory when `--path-prefix` is not used, and a relative path that is configured using `--path-prefix` is resolved against the current working directory

## [13.2.1] - 2026-11-02

### Changed

* Make PHAR build reproducible across filesystems (files are now added to the PHAR sorted by their path instead of in the order in which the filesystem lists them)

## [13.2.0] - 2026-10-05

### Added

* Added `--jsonl` CLI option for merging into JSONL format

## [13.1.1] - 2026-09-25

### Fixed

* [#237](https://github.com/sebastianbergmann/phpcov/issues/237): Support for serialization format 4 of phpunit/php-code-coverage 14.3.5

## [13.1.0] - 2026-08-07

### Added

* Added support for phpunit/php-code-coverage 14.3.0
* Added `--html-views` CLI option

## [13.0.1] - 2026-04-18

### Added

* Added support for phpunit/php-code-coverage 14.1.3

## [13.0.0] - 2026-04-03

### Added

* [#109](https://github.com/sebastianbergmann/phpcov/issues/109): Support for merging serialized code coverage files generated using PHPUnit's PHAR
* [#115](https://github.com/sebastianbergmann/phpcov/issues/115): Support for merging of serialized code coverage data files generated with different base paths
* Added support for PHPUnit 13.1 and phpunit/php-code-coverage 14.0
* Added `--openclover` CLI option for merging into OpenClover XML format
* Added `--source` CLI option for configuring the path to source code when merging on a different machine
* Added `--do-not-require-matching-git-information`, `--do-not-require-matching-php-version`, and `--do-not-require-matching-code-coverage-driver` CLI options for the `merge` command

### Fixed

* [#153](https://github.com/sebastianbergmann/phpcov/pull/153): Patches modifying only uncovered lines are not handled correctly

### Removed

* The "execute" command has been removed
* PHPUnit 13.0 and phpunit/php-code-coverage 13.0 are no longer supported

## [12.0.0] - 2026-02-06

### Added

* Added support for PHPUnit 13

### Removed

* PHPUnit 12 is no longer supported
* PHP 8.3 is no longer supported

## [11.0.4] - 2026-02-06

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [11.0.3] - 2025-11-29

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [11.0.2] - 2025-09-02

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [11.0.1] - 2025-02-29

* No changes; `phpcov.phar` rebuilt with updated dependencies

## [11.0.0] - 2025-02-07

### Added

* Added support for PHPUnit 12

### Removed

* PHPUnit 11 is no longer supported
* PHP 8.2 is no longer supported

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

[13.3.0]: https://github.com/sebastianbergmann/phpcov/compare/13.2.1...main
[13.2.1]: https://github.com/sebastianbergmann/phpcov/compare/13.2.0...13.2.1
[13.2.0]: https://github.com/sebastianbergmann/phpcov/compare/13.1.1...13.2.0
[13.1.1]: https://github.com/sebastianbergmann/phpcov/compare/13.1.0...13.1.1
[13.1.0]: https://github.com/sebastianbergmann/phpcov/compare/13.0.1...13.1.0
[13.0.1]: https://github.com/sebastianbergmann/phpcov/compare/13.0.0...13.0.1
[13.0.0]: https://github.com/sebastianbergmann/phpcov/compare/12.0.0...13.0.0
[12.0.0]: https://github.com/sebastianbergmann/phpcov/compare/11.0.4...12.0.0
[11.0.4]: https://github.com/sebastianbergmann/phpcov/compare/11.0.3...11.0.4
[11.0.3]: https://github.com/sebastianbergmann/phpcov/compare/11.0.2...11.0.3
[11.0.2]: https://github.com/sebastianbergmann/phpcov/compare/11.0.1...11.0.2
[11.0.1]: https://github.com/sebastianbergmann/phpcov/compare/11.0.0...11.0.1
[11.0.0]: https://github.com/sebastianbergmann/phpcov/compare/10.0.1...11.0.0
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

