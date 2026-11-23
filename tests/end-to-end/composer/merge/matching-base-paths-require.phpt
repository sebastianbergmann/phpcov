--TEST--
phpcov merge --require-matching-base-paths --text /tmp/file ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--require-matching-base-paths';
$_SERVER['argv'][3] = '--text';
$_SERVER['argv'][4] = $tmp;
$_SERVER['argv'][5] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in text format ... done
int(0)
