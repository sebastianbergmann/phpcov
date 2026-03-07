--TEST--
phpcov
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Usage:
  phpcov merge          [options] <directory with *.cov files>
  phpcov patch-coverage [options] <coverage file> <patch file>

Options for "phpcov merge":

  --clover <file>        Generate code coverage report in Clover XML format
  --openclover <file>    Generate code coverage report in OpenClover XML format
  --cobertura <file>     Generate code coverage report in Cobertura XML format
  --crap4j <file>        Generate code coverage report in Crap4J XML format
  --html <directory>     Generate code coverage report in HTML format
  --php <file>           Export php-code-coverage object
  --source <directory>   Path to source code (when merging on a different machine)
  --text <file>          Generate code coverage report in text format
  --xml <directory>      Generate code coverage report in PHPUnit XML format

Options for "phpcov patch-coverage":

  --path-prefix <prefix> Prefix that needs to be stripped from paths in the patch
int(255)
