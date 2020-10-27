# phpcov

**phpcov** is a command-line frontend for the php-code-coverage library.

## Installation

This tool is distributed as a [PHP Archive (PHAR)](https://php.net/phar):

```bash
$ wget https://phar.phpunit.de/phpcov.phar

$ php phpcov.phar --version
```

Using [Phive](https://phar.io/) is the recommended way for managing the tool dependencies of your project:

```bash
$ phive install phpcov

$ ./tools/phpcov --version
```

**[It is not recommended to use Composer to download and install this tool.](https://twitter.com/s_bergmann/status/999635212723212288)**

## Usage

### Executing a PHP script and generating code coverage in Clover XML format

    $ phpcov execute --clover coverage.xml script.php
    phpcov 8.1.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done

### Merging exported php-code-coverage objects stored in *.cov files

    $ parallel --gnu :::                                                 \
        'phpunit --coverage-php /tmp/coverage/FooTest.cov tests/FooTest' \
        'phpunit --coverage-php /tmp/coverage/BarTest.cov tests/BarTest'

    $ phpcov merge --clover /tmp/clover.xml /tmp/coverage
    phpcov 8.1.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done

### Patch Coverage

    $ git diff HEAD^1 > /tmp/patch.txt

    $ phpunit --coverage-php /tmp/coverage.cov

    $ phpcov patch-coverage --path-prefix /path/to/project /tmp/coverage.cov /tmp/patch.txt
    phpcov 8.1.0 by Sebastian Bergmann.

    1 / 2 changed executable lines covered (50.00%)

    Changed executable lines that are not covered:

      Example.php:11
