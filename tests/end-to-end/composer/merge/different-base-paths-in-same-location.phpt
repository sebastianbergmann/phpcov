--TEST--
phpcov merge --php /tmp/file <directory with .cov files with base paths that are below one another>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Report\Facade;
use SebastianBergmann\CodeCoverage\Serialization\Merger;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

$fixture = __DIR__ . '/../../../fixture/example';

// The base path of this code coverage data is the directory with the source files
$below = (new Unserializer)->unserialize($fixture . '/coverage/testGreetsWorld.cov');

// The base path of this code coverage data is the directory above the directory with the source files
$above = (new Unserializer)->unserialize($fixture . '/coverage/testGreetsWithName.cov');

foreach ($above['codeCoverage']->coveredFiles() as $file) {
    $above['codeCoverage']->renameFile($file, 'src' . DIRECTORY_SEPARATOR . $file);
}

$above['basePath'] = dirname($above['basePath']);

// This code coverage data is for no source files and has no base path
$empty                 = $below;
$empty['basePath']     = '';
$empty['codeCoverage'] = new ProcessedCodeCoverageData;
$empty['testResults']  = [];

// What is expected is what merging the original files, which have the same base path, results in
$expected = Facade::fromSerializedData(
    (new Merger)->merge(
        [
            $fixture . '/coverage/testGreetsWorld.cov',
            $fixture . '/coverage/testGreetsWithName.cov',
        ],
    ),
)->summary();

$orders = [
    'below, above'        => [$below, $above],
    'above, below'        => [$above, $below],
    'empty, below, above' => [$empty, $below, $above],
];

foreach ($orders as $order => $files) {
    $covDir = sys_get_temp_dir() . '/phpcov_merge_' . uniqid();
    mkdir($covDir);

    foreach ($files as $index => $data) {
        (new Serializer)->write($covDir . '/' . $index . '.cov', $data);
    }

    $tmp = tempnam(sys_get_temp_dir(), __FILE__);

    $_SERVER['argv'] = [$_SERVER['argv'][0], 'merge', '--require-matching-base-paths', '--php', $tmp, $covDir];

    ob_start();
    $exitCode = (new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']);
    ob_end_clean();

    $merged  = (new Unserializer)->unserialize($tmp);
    $summary = Facade::fromSerializedData($merged)->summary();

    print $order . PHP_EOL;
    var_dump($exitCode);
    var_dump($merged['basePath'] === dirname(realpath($fixture . '/src')));
    var_dump($merged['codeCoverage']->coveredFiles() === ['src' . DIRECTORY_SEPARATOR . 'Greeter.php', 'src' . DIRECTORY_SEPARATOR . 'autoload.php']);
    var_dump(count($merged['testResults']));
    var_dump($summary->numberOfExecutableLines() === $expected->numberOfExecutableLines());
    var_dump($summary->numberOfExecutedLines() === $expected->numberOfExecutedLines());
    var_dump($summary->numberOfExecutedLines() > 0);

    unlink($tmp);

    array_map('unlink', glob($covDir . '/*.cov'));
    rmdir($covDir);
}
--EXPECT--
below, above
int(0)
bool(true)
bool(true)
int(2)
bool(true)
bool(true)
bool(true)
above, below
int(0)
bool(true)
bool(true)
int(2)
bool(true)
bool(true)
bool(true)
empty, below, above
int(0)
bool(true)
bool(true)
int(2)
bool(true)
bool(true)
bool(true)
