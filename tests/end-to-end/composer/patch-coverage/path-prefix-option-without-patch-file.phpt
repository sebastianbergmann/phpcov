--TEST--
phpcov patch-coverage --path-prefix /path/prefix ../../fixture/example/coverage/testGreetsWorld.cov
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = dirname(__DIR__, 4);
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

The --path-prefix option can only be used together with a patch file
int(255)
