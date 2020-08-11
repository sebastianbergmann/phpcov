--TEST--
phpcov merge ../../fixture/empty-directory
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = __DIR__ . '/../../fixture/empty-directory';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

No "%s*.cov" files found
int(1)
