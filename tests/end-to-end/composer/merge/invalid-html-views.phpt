--TEST--
phpcov merge --html /tmp/dir --html-views invalid ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--html';
$_SERVER['argv'][3] = sys_get_temp_dir() . '/phpcov-html-' . uniqid();
$_SERVER['argv'][4] = '--html-views';
$_SERVER['argv'][5] = 'invalid';
$_SERVER['argv'][6] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Invalid value "invalid" for "--html-views", expected a comma-separated list of "file" and "class"
int(255)
