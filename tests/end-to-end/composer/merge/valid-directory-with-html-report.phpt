--TEST--
phpcov merge --html /tmp/dir ../../fixture/example/coverage
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$tmp = sys_get_temp_dir() . '/phpcov-html-' . uniqid();

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--html';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = __DIR__ . '/../../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

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
