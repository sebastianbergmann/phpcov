--TEST--
phpcov merge --do-not-require-matching-code-coverage-driver --text /tmp/file ../../fixture/mismatching-driver/coverage
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--do-not-require-matching-code-coverage-driver';
$_SERVER['argv'][3] = '--text';
$_SERVER['argv'][4] = $tmp;
$_SERVER['argv'][5] = __DIR__ . '/../../fixture/mismatching-driver/coverage';

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
  Classes: 100.00% (1/1)
  Methods: 100.00% (2/2)
  Lines:   66.67% (2/3)

SebastianBergmann\PHPCOV\TestFixture\Greeter
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  2/  2)
