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

    cat add.php
    <?php
    $a = 1;
    $b = 2;
    print $a + $b;
    ?>

    phpcov --clover clover.xml add.php
    phpcov 1.1.0 by Sebastian Bergmann.

    3

    cat clover.xml
    <?xml version="1.0" encoding="UTF-8"?>
    <coverage generated="1270365900">
      <project timestamp="1270365900">
        <file name="/usr/local/src/bytekit-cli/examples/add.php">
          <line num="2" type="stmt" count="1"/>
          <line num="3" type="stmt" count="1"/>
          <line num="4" type="stmt" count="1"/>
          <line num="5" type="stmt" count="1"/>
          <metrics loc="4" ncloc="4" classes="0" methods="0"
                   coveredmethods="0" conditionals="0"
                   coveredconditionals="0" statements="4"
                   coveredstatements="4" elements="4"
                   coveredelements="4"/>
        </file>
        <metrics files="1" loc="4" ncloc="4" classes="0" methods="0"
                 coveredmethods="0" conditionals="0"
                 coveredconditionals="0" statements="4"
                 coveredstatements="4" elements="4"
                 coveredelements="4"/>
      </project>
    </coverage>
