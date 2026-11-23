--TEST--
phpcov patch-coverage --base main ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--base';
$_SERVER['argv'][3] = 'main';
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][5] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

The --base option cannot be used together with a patch file
int(255)
