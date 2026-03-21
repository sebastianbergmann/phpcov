--TEST--
phpcov patch-coverage --path-prefix /path/prefix ../../fixture/example/coverage/testGreetsWithName.cov ../../fixture/example/patch
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = dirname(__DIR__, 4);
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWithName.cov';
$_SERVER['argv'][5] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

1 / 1 changed executable lines covered (100.00%)
int(0)
