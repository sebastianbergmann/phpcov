--TEST--
phpcov merge --openclover /tmp/file --source /tmp/source <directory with .cov files from different base paths>
--INI--
xdebug.overload_var_dump=0
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

$sourceDir = sys_get_temp_dir() . '/phpcov_source_' . uniqid();
mkdir($sourceDir);

copy(__DIR__ . '/../../../fixture/example/src/Greeter.php', $sourceDir . '/Greeter.php');
copy(__DIR__ . '/../../../fixture/example/src/autoload.php', $sourceDir . '/autoload.php');
copy(__DIR__ . '/../../../fixture/example3/src/Calculator.php', $sourceDir . '/Calculator.php');

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--openclover';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = '--source';
$_SERVER['argv'][5] = $sourceDir;
$_SERVER['argv'][6] = $covDir;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

$xml = file_get_contents($tmp);

var_dump(str_contains($xml, 'Greeter.php'));
var_dump(str_contains($xml, 'Calculator.php'));

$dom = new DOMDocument;
var_dump($dom->loadXML($xml));
var_dump($dom->documentElement->tagName);

unlink($tmp);

array_map('unlink', glob($covDir . '/*.cov'));
rmdir($covDir);

array_map('unlink', glob($sourceDir . '/*.php'));
rmdir($sourceDir);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in OpenClover XML format ... done
int(0)
bool(true)
bool(true)
bool(true)
string(8) "coverage"
