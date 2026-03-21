--TEST--
phpcov patch-coverage with invalid coverage file content
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = tempnam(sys_get_temp_dir(), __FILE__);
file_put_contents($tmp, 'this is not valid coverage data');

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $tmp;
$_SERVER['argv'][3] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

%s
int(255)
