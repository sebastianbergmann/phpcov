--TEST--
phpcov merge does-not-exist
--INI--
xdebug.mode=debug
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = 'does-not-exist';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

"does-not-exist" is not a directory
int(1)
