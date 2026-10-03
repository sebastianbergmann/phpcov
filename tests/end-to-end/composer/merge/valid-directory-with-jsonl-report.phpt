--TEST--
phpcov merge --jsonl /tmp/dir ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = sys_get_temp_dir() . '/phpcov-jsonl-' . uniqid();

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--jsonl';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

print file_get_contents($tmp . '/meta.json');
print file_get_contents($tmp . '/coverage.jsonl');
print file_get_contents($tmp . '/tests.jsonl');

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tmp, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST,
);

foreach ($files as $file) {
    $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
}

rmdir($tmp);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in JSONL format ... done
int(0)
{
    "schemaVersion": 1,
    "generator": "php-code-coverage %s",
    "generatedAt": "%s",
    "sourceRoot": "%s",
    "branchCoverage": false,
    "files": 2,
    "executableLines": 3,
    "executedLines": 2
}
{"file":"Greeter.php","executable":2,"executed":2,"symbols":[{"name":"SebastianBergmann\\PHPCOV\\TestFixture\\Greeter::greetWorld","lines":"14-17","state":"covered"},{"name":"SebastianBergmann\\PHPCOV\\TestFixture\\Greeter::greetWithName","lines":"19-22","state":"covered"}]}
{"file":"autoload.php","executable":1,"executed":0,"uncovered":["11"]}
{"test":"SebastianBergmann\\PHPCOV\\TestFixture\\GreeterTest::testGreetsWithName","covers":{"Greeter.php":["21"]}}
{"test":"SebastianBergmann\\PHPCOV\\TestFixture\\GreeterTest::testGreetsWorld","covers":{"Greeter.php":["16"]}}
