[![Latest Stable Version](https://poser.pugx.org/phpunit/phpcov/v)](https://packagist.org/packages/phpunit/phpcov)
[![CI Status](https://github.com/sebastianbergmann/phpcov/workflows/CI/badge.svg)](https://github.com/sebastianbergmann/phpcov/actions)
[![codecov](https://codecov.io/gh/sebastianbergmann/phpcov/branch/main/graph/badge.svg)](https://codecov.io/gh/sebastianbergmann/phpcov)

# phpcov

**phpcov** is a command-line frontend for the php-code-coverage library.

## Installation

This tool is distributed as a [PHP Archive (PHAR)](https://php.net/phar):

```bash
$ wget https://phar.phpunit.de/phpcov-X.Y.phar

$ php phpcov-X.Y.phar --version
```

Please replace `X.Y` with the version of PHPCOV you are interested in.

Using [Phive](https://phar.io/) is the recommended way for managing the tool dependencies of your project:

```
$ phive install phpcov

$ ./tools/phpcov --version
```

**[It is not recommended to use Composer to download and install this tool.](https://phpunit.readthedocs.io/en/11.0/installation.html#phar-or-composer)**

## Usage

### Merging serialized code coverage data

When you run PHPUnit with `--coverage-php`, it writes serialized code coverage data to a `.cov` file.
The `phpcov merge` command merges multiple `.cov` files into a single code coverage report.

This is useful when you run test suites in parallel or across separate processes and want to combine the results into one report.

#### Example

Run your test suites separately, each writing their own `.cov` file:

```
$ phpunit --coverage-php /tmp/coverage/FooTest.cov tests/FooTest
$ phpunit --coverage-php /tmp/coverage/BarTest.cov tests/BarTest
```

Then merge the `.cov` files and generate a report:

```
$ phpcov merge --html /tmp/coverage-html /tmp/coverage
```

All `.cov` files in the specified directory will be merged.

You can generate reports in multiple formats at once:

```
$ phpcov merge --html /tmp/coverage-html --openclover /tmp/coverage.xml /tmp/coverage
```

#### Choosing the views of the HTML report

The HTML report has a file-oriented view (directories and files) and a class-oriented view (namespaces and classes). By default, both are generated.

Use `--html-views` to generate only one of them:

| Value        | Effect                                     |
|--------------|--------------------------------------------|
| `file`       | Only the file-oriented view                |
| `class`      | Only the class-oriented view               |
| `file,class` | Both views (default)                       |

```
$ phpcov merge --html /tmp/coverage-html --html-views class /tmp/coverage
```

#### Exporting merged data

Use `--php` to write the merged coverage data back to a `.cov` file:

```
$ phpcov merge --php /tmp/merged.cov /tmp/coverage
```

#### Merging on a different machine

The serialized `.cov` files store file paths relative to a base path (the common path prefix of the files that code coverage was collected for).
When generating reports, the source files must be readable so that they can be analysed.

If you are merging `.cov` files on a different machine than where the tests were run, and the source code is located at a different path, use `--source` to specify where the source code is on the current machine:

```
$ phpcov merge --source /home/ci/project --html /tmp/coverage-html /tmp/coverage
```

When `--php` is used together with `--source`, the `.cov` file that is written has the path given with `--source` as its base path.

#### Relaxing merge requirements

By default, `phpcov merge` requires that all `.cov` files were created using the same PHP version, the same code coverage driver, the same granularity (line coverage, or line and branch coverage, or line, branch, and path coverage), and (when present) the same Git state. If these do not match, the merge will fail.

You can relax these requirements with the following options:

| Option                                           | Effect                                              |
|--------------------------------------------------|-----------------------------------------------------|
| `--do-not-require-matching-git-information`      | Allow merging files with different git state        |
| `--do-not-require-matching-php-version`          | Allow merging files from different PHP versions     |
| `--do-not-require-matching-code-coverage-driver` | Allow merging files from different coverage drivers |
| `--do-not-require-matching-granularity`          | Allow merging files with different granularity      |

For example, to merge `.cov` files that were collected with different PHP versions:

```
$ phpcov merge --do-not-require-matching-php-version --html /tmp/coverage-html /tmp/coverage
```

Branch and path coverage that is merged with code coverage data that was collected without it is incomplete: what was executed while the latter was collected is missing from it.

#### Requiring a single location

The paths in a `.cov` file are relative to its base path. `.cov` files that were written in different locations, by CI jobs that check the project out into different directories for instance, have different base paths and are merged using these relative paths.

Use `--require-matching-base-paths` when all `.cov` files are expected to come from the same location. The merge then fails when the base paths of two files are not the same directory and neither is a directory below the other:

```
$ phpcov merge --require-matching-base-paths --html /tmp/coverage-html /tmp/coverage
```

#### Available Report Formats

| Option                | Format                  |
|-----------------------|-------------------------|
| `--clover <file>`     | Clover XML              |
| `--openclover <file>` | OpenClover XML          |
| `--cobertura <file>`  | Cobertura XML           |
| `--crap4j <file>`     | Crap4J XML              |
| `--html <directory>`  | HTML                    |
| `--jsonl <directory>` | JSONL                   |
| `--php <file>`        | Serialized PHP (`.cov`) |
| `--text <file>`       | Plain text              |
| `--xml <directory>`   | PHPUnit XML             |

### Patch Coverage

The `phpcov patch-coverage` command calculates code coverage for the lines changed in a patch (unified diff).
It reports how many of the changed executable lines are covered by tests.

#### Example

Generate a unified diff and collect code coverage:

```
$ git diff HEAD^1 > /tmp/patch.txt
$ phpunit --coverage-php /tmp/coverage.cov
```

Then calculate patch coverage:

```
$ phpcov patch-coverage /tmp/coverage.cov /tmp/patch.txt
1 / 2 changed executable lines covered (50.00%)

Changed executable lines that are not covered:

  Example.php:11
```

The command exits with code `0` when all changed executable lines are covered.
It exits with `1` when some changed executable lines are not covered.
It exits with `2` when no changed executable lines could be detected. This usually indicates a path mismatch.

#### The `--path-prefix` option

The paths in a patch are relative to the directory the patch was generated for. For a patch that was generated using `git diff`, this is the root directory of the repository.

`phpcov patch-coverage` expects this directory to be the current working directory. Use `--path-prefix` when it is not:

```
$ phpcov patch-coverage --path-prefix /path/to/project /tmp/coverage.cov /tmp/patch.txt
```

A relative path is resolved against the current working directory.
