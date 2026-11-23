--TEST--
phpcov merge --do-not-require-matching-granularity --text /tmp/file <directory with .cov files that were written with different granularity>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$covDir = sys_get_temp_dir() . '/phpcov_merge_' . uniqid();
mkdir($covDir);

foreach (['testGreetsWorld' => 'LineBranchAndPath', 'testGreetsWithName' => 'Line'] as $name => $granularity) {
    $data = (new SebastianBergmann\CodeCoverage\Serialization\Unserializer)->unserialize(
        __DIR__ . '/../../../fixture/example/coverage/' . $name . '.cov',
    );

    $data['buildInformation']['phpCodeCoverage']['driverInformation']['granularity'] = $granularity;

    (new SebastianBergmann\CodeCoverage\Serialization\Serializer)->write($covDir . '/' . $name . '.cov', $data);
}

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--do-not-require-matching-granularity';
$_SERVER['argv'][3] = '--text';
$_SERVER['argv'][4] = $tmp;
$_SERVER['argv'][5] = $covDir;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);

array_map('unlink', glob($covDir . '/*.cov'));
rmdir($covDir);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in text format ... done
int(0)
