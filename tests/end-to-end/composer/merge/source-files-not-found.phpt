--TEST--
phpcov merge --text /tmp/file --source /does/not/exist ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--text';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = '--source';
$_SERVER['argv'][5] = '/does/not/exist';
$_SERVER['argv'][6] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Warning: The code coverage data of 2 files is not part of the reports because these files were not found:

  /does/not/exist/Greeter.php
  /does/not/exist/autoload.php

The path configured using --source is "/does/not/exist". Is this where the source code is located?

Generating code coverage report in text format ... done
int(0)
