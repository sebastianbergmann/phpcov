--TEST--
phpcov patch-coverage does-not-exist does-not-exist
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = 'does-not-exist';
$_SERVER['argv'][3] = 'does-not-exist';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Code Coverage file "does-not-exist" does not exist
int(255)
