# phpcov

**phpcov** is a command-line frontend for the PHP_CodeCoverage library.

## Installation

There are two supported ways of installing phpcov.

You can use the [PEAR Installer](http://pear.php.net/manual/en/guide.users.commandline.cli.php) to download and install phpcov as well as its dependencies. You can also download a [PHP Archive (PHAR)](http://php.net/phar) of phpcov that has all required dependencies of phpcov bundled in a single file.

### PEAR Installer

The following two commands (which you may have to run as `root`) are all that is required to install phpcov using the PEAR Installer:

    pear config-set auto_discover 1
    pear install pear.phpunit.de/phpcov

### PHP Archive (PHAR)

    wget http://pear.phpunit.de/get/phpcov.phar
    chmod +x phpcov.phar

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
