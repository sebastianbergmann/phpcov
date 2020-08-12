--TEST--
phpcov execute --include ../../fixture/test.php --text /tmp/file ../../fixture/test.php
--INI--
xdebug.overload_var_dump=0
--SKIPIF--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;

try {
    $filter = new Filter;

    new CodeCoverage(
        Driver::forLineCoverage($filter),
        $filter
    );
} catch (Exception $e) {
    print 'skip: ' . $e->getMessage();
}
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'execute';
$_SERVER['argv'][2] = '--include';
$_SERVER['argv'][3] = __DIR__ . '/../../fixture/test.php';
$_SERVER['argv'][4] = '--text';
$_SERVER['argv'][5] = $tmp;
$_SERVER['argv'][6] = __DIR__ . '/../../fixture/test.php';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

print file_get_contents($tmp);

unlink($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in text format ... done
int(0)


Code Coverage Report:  
  %s  
                       
 Summary:              
  Classes:        (0/0)
  Methods:        (0/0)
  Lines:   50.00% (1/2)

