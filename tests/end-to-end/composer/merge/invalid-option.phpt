--TEST--
phpcov merge --unknown-option ../../fixture/example/coverage
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--unknown-option';
$_SERVER['argv'][3] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

%s
int(255)
