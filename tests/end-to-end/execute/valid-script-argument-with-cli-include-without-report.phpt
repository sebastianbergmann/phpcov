--TEST--
phpcov execute --include ../../fixture/test.php ../../fixture/test.php
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'execute';
$_SERVER['argv'][2] = '--include';
$_SERVER['argv'][3] = __DIR__ . '/../../fixture/test.php';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/test.php';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

No code coverage report configured
int(1)
