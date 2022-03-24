--TEST--
phpcov execute does-not-exist.php
--INI--
xdebug.mode=debug
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'execute';
$_SERVER['argv'][2] = 'does-not-exist.php';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

File "does-not-exist.php" does not exist
int(1)
