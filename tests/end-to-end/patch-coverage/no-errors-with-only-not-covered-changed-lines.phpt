--TEST--
phpcov patch-coverage --path-prefix /path/prefix ../../fixture/example2/coverage/testGreetsWorld2.cov ../../fixture/example2/patch2
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = '/tmp/tmp.eeK19HW3Mj/phpcov/';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/example2/coverage/testGreetsWorld2.cov';
$_SERVER['argv'][5] = __DIR__ . '/../../fixture/example2/patch2';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

0 / 0 changed executable lines covered ()
int(0)
