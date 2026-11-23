--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov ../../fixture/example/patch (in a directory in which the files that the patch changes do not exist)
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

chdir(dirname(__DIR__, 3));

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][3] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Unable to find the files that the patch changes.
The paths in the patch were resolved against "%s", the code coverage data is for files in "%s".
Are you sure you do not need to use --path-prefix?
int(2)
