phpcov
======

**phpcov** is a command-line frontend for the PHP_CodeCoverage library.

Installation
------------

phpcov should be installed using the PEAR Installer, the backbone of the [PHP Extension and Application Repository](http://pear.php.net/) that provides a distribution system for PHP packages.

Depending on your OS distribution and/or your PHP environment, you may need to install PEAR or update your existing PEAR installation before you can proceed with the following instructions. `sudo pear upgrade PEAR` usually suffices to upgrade an existing PEAR installation. The [PEAR Manual ](http://pear.php.net/manual/en/installation.getting.php) explains how to perform a fresh installation of PEAR.

The following two commands are all that is required to install phpcov using the PEAR Installer:

    pear config-set auto_discover 1
    pear install pear.phpunit.de/phpcov

Usage
-----

    sb@vmware examples % cat add.php
    <?php
    $a = 1;
    $b = 2;
    print $a + $b;

    sb@vmware examples % phpcov --clover clover.xml add.php
    phpcov 1.0.0 by Sebastian Bergmann.

    3

    sb@vmware examples % cat clover.xml
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
