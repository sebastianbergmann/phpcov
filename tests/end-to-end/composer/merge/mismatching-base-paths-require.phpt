--TEST--
phpcov merge --require-matching-base-paths --text /tmp/file <directory with .cov files from different base paths>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$covDir = sys_get_temp_dir() . '/phpcov_merge_' . uniqid();
mkdir($covDir);

foreach (glob(__DIR__ . '/../../../fixture/example/coverage/*.cov') as $f) {
    copy($f, $covDir . '/' . basename($f));
}

foreach (glob(__DIR__ . '/../../../fixture/example3/coverage/*.cov') as $f) {
    copy($f, $covDir . '/' . basename($f));
}

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--require-matching-base-paths';
$_SERVER['argv'][3] = '--text';
$_SERVER['argv'][4] = $tmp;
$_SERVER['argv'][5] = $covDir;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);

array_map('unlink', glob($covDir . '/*.cov'));
rmdir($covDir);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Base path mismatch: "%s" and "%s" are not the same directory, and neither is a directory below the other
int(255)
