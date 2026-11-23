--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch (in the directory the paths in the patch are relative to)
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

chdir(dirname(__DIR__, 4));

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][3] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  tests/fixture/example/src/Greeter.php:21
int(1)
