# phpcov

**phpcov** is a command-line frontend for the PHP_CodeCoverage library.

## Installation

### PHP Archive (PHAR)

The easiest way to obtain PHPCOV is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of PHPCOV bundled in a single file:

    wget https://phar.phpunit.de/phpcov.phar
    chmod +x phpcov.phar
    mv phpcov.phar /usr/local/bin/phpcov

You can also immediately use the PHAR after you have downloaded it, of course:

    wget https://phar.phpunit.de/phpcov.phar
    php phpcov.phar

### Composer

Simply add a dependency on `phpunit/phpcov` to your project's `composer.json` file if you use [Composer](http://getcomposer.org/) to manage the dependencies of your project. Here is a minimal example of a `composer.json` file that just defines a development-time dependency on PHPCOV:

    {
        "require-dev": {
            "phpunit/phpcov": "*"
        }
    }

For a system-wide installation via Composer, you can run:

    composer global require 'phpunit/phpcov=*'

Make sure you have `~/.composer/vendor/bin/` in your path.

### PEAR Installer

The following two commands (which you may have to run as `root`) are all that is required to install PHPCOV using the PEAR Installer:

    pear config-set auto_discover 1
    pear install pear.phpunit.de/phpcov

## Usage

### Patch Coverage

    phpcov patch-coverage --patch patch.txt --path-prefix /home/sb/example/ example.cov
    phpcov 2.0.0 by Sebastian Bergmann.

    1 / 2 changed executable lines covered (50.00%)

    Changed executable lines that are not covered:

      Example.php:11

### Merging exported PHP_CodeCoverage objects stored in *.cov files

    phpcov merge --clover coverage.xml /home/sb/example
    phpcov 2.0.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done

### Executing a PHP script and generating code coverage in Clover XML format

    phpcov execute --clover coverage.xml script.php
    phpcov 2.0.0 by Sebastian Bergmann.

    Generating code coverage report in Clover XML format ... done
