--TEST--
phpcov merge --text /tmp/file ../../fixture/mismatching-php-version/coverage
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--text';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/mismatching-php-version/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Not all files were created using the same runtime
int(255)
