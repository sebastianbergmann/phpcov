--TEST--
phpcov patch-coverage --path-prefix invalid ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = 'invalid';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][5] = __DIR__ . '/../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Unable to detect executable lines that were changed.
Are you sure your --path-prefix is correct?
int(2)
