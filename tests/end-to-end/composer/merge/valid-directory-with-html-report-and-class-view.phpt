--TEST--
phpcov merge --html /tmp/dir --html-views class ../../fixture/example/coverage
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = sys_get_temp_dir() . '/phpcov-html-' . uniqid();

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--html';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = '--html-views';
$_SERVER['argv'][5] = 'class';
$_SERVER['argv'][6] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

var_dump(is_file($tmp . '/Greeter.html'));
var_dump(is_file($tmp . '/Greeter.php.html'));

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

Generating code coverage report in HTML format ... done
int(0)
bool(true)
bool(false)
