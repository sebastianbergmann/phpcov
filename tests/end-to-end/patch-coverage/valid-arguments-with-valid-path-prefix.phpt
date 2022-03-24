--TEST--
phpcov patch-coverage --path-prefix /path/prefix ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch
--INI--
xdebug.mode=debug
xdebug.mode=coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = '/usr/local/src/phpcov';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][5] = __DIR__ . '/../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  tests/fixture/example/src/Greeter.php:21
int(1)
