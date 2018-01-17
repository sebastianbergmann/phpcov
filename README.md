# phpcov

**phpcov** is a command-line frontend for the php-code-coverage library.

## Installation

### PHP Archive (PHAR)

The easiest way to obtain phpcov is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of phpcov bundled in a single file:

    $ wget https://phar.phpunit.de/phpcov.phar
    $ chmod +x phpcov.phar
    $ mv phpcov.phar /usr/local/bin/phpcov

You can also immediately use the PHAR after you have downloaded it, of course:

    $ wget https://phar.phpunit.de/phpcov.phar
    $ php phpcov.phar

### Composer

You can add this tool as a local, per-project, development-time dependency to your project using [Composer](https://getcomposer.org/):

    $ composer require --dev phpunit/phpcov

You can then invoke it using the `vendor/bin/phpcov` executable.

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
