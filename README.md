# phpcov

**phpcov** is a command-line frontend for the php-code-coverage library.

## Installation

The recommended way to use this tool is a [PHP Archive (PHAR)](https://php.net/phar):

```bash
$ wget https://phar.phpunit.de/phpcov.phar

$ php phpcov.phar --version
```

Furthermore, it is recommended to use [Phive](https://phar.io/) for installing and updating the tool dependencies of your project.

Alternatively, you may use [Composer](https://getcomposer.org/) to download and install this tool as well as its dependencies. [This is not recommended, though.](https://twitter.com/s_bergmann/status/999635212723212288)

## Usage

### Patch Coverage

    $ git diff HEAD^1 > /tmp/patch.txt

    $ phpunit --coverage-php /tmp/coverage.cov

    $ phpcov patch-coverage /tmp/coverage.cov              \
                            --patch /tmp/patch.txt         \
                            --path-prefix /path/to/project
    phpcov 2.0.0 by Sebastian Bergmann.

    1 / 2 changed executable lines covered (50.00%)

    Changed executable lines that are not covered:

      Example.php:11

### Merging exported php-code-coverage objects stored in *.cov files

    $ parallel --gnu :::                                                 \
        'phpunit --coverage-php /tmp/coverage/FooTest.cov tests/FooTest' \
        'phpunit --coverage-php /tmp/coverage/BarTest.cov tests/BarTest'

    $ phpcov merge /tmp/coverage --clover /tmp/clover.xml
    phpcov 2.0.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done

### Executing a PHP script and generating code coverage in Clover XML format

    $ phpcov execute script.php --clover coverage.xml
    phpcov 2.0.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done
