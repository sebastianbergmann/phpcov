--TEST--
phpcov
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = '--help';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Usage:
  phpcov execute       [options] <script>
  phpcov merge         [options] <directory with *.cov files>
  phpcov path-coverage [options] <coverage file> <patch file>

Options for "phpcov execute":

  --configuration <file> Load PHPUnit configuration from XML configuration
  --include <directory>  Include <directory> in code coverage analysis
  --path-coverage        Perform path coverage analysis
  --add-uncovered        Include uncovered files in code coverage report
  --process-uncovered    Process uncovered file for code coverage report

Options common for both "phpcov execute" and "phpcov merge":

  --clover <file>        Generate code coverage report in Clover XML format
  --cobertura <file>     Generate code coverage report in Cobertura XML format
  --crap4j <file>        Generate code coverage report in Crap4J XML format
  --html <directory>     Generate code coverage report in HTML format
  --php <file>           Export php-code-coverage object
  --text <file>          Generate code coverage report in text format
  --summary-only         Output only summary in the text format report
  --xml <directory>      Generate code coverage report in PHPUnit XML format

Options for "phpcov patch-coverage":

  --path-prefix <prefix> Prefix that needs to be stripped from paths in the patch
int(0)
