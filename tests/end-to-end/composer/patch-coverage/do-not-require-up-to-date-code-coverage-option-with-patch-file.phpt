--TEST--
phpcov patch-coverage --do-not-require-up-to-date-code-coverage ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--do-not-require-up-to-date-code-coverage';
$_SERVER['argv'][3] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

The --do-not-require-up-to-date-code-coverage option cannot be used together with a patch file
int(255)
