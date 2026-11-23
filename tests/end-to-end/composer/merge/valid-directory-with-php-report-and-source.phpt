--TEST--
phpcov merge --php /tmp/file --source /tmp/source ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$sourceDir = sys_get_temp_dir() . '/phpcov_source_' . uniqid();
mkdir($sourceDir);

copy(__DIR__ . '/../../../fixture/example/src/Greeter.php', $sourceDir . '/Greeter.php');
copy(__DIR__ . '/../../../fixture/example/src/autoload.php', $sourceDir . '/autoload.php');

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--php';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = '--source';
$_SERVER['argv'][5] = $sourceDir;
$_SERVER['argv'][6] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

$data = (new SebastianBergmann\CodeCoverage\Serialization\Unserializer)->unserialize($tmp);

var_dump($data['basePath'] === $sourceDir);
var_dump(SebastianBergmann\CodeCoverage\Report\Facade::fromSerializedData($data)->summary()->numberOfExecutableLines() > 0);

unlink($tmp);

array_map('unlink', glob($sourceDir . '/*.php'));
rmdir($sourceDir);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in PHP format ... done
int(0)
bool(true)
bool(true)
