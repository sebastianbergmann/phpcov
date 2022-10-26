--TEST--
phpcov execute --text /tmp/file ../../fixture/test.php
--INI--
xdebug.overload_var_dump=0
--SKIPIF--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

use SebastianBergmann\CodeCoverage\Driver\Selector as DriverSelector;
use SebastianBergmann\CodeCoverage\Filter;

try {
    (new DriverSelector)->forLineCoverage(new Filter);
} catch (Exception $e) {
    print 'skip: ' . $e->getMessage();
}
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'execute';
$_SERVER['argv'][2] = '--text';
$_SERVER['argv'][3] = '/tmp/file';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/test.php';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

No list of files to be included in code coverage configured
int(1)
