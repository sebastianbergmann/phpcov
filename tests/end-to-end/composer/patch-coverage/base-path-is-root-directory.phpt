--TEST--
phpcov patch-coverage --path-prefix /path/prefix <.cov file with the root directory as its base path> ../../fixture/example/patch
--SKIPIF--
<?php declare(strict_types=1);
if (DIRECTORY_SEPARATOR !== '/') {
    print 'skip: requires a file system with a root directory';
}
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$data = (new SebastianBergmann\CodeCoverage\Serialization\Unserializer)->unserialize(
    __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov',
);

$prefix = ltrim($data['basePath'], '/');

foreach ($data['codeCoverage']->coveredFiles() as $file) {
    $data['codeCoverage']->renameFile($file, $prefix . '/' . $file);
}

$data['basePath'] = '/';

$coverage = tempnam(sys_get_temp_dir(), __FILE__);

(new SebastianBergmann\CodeCoverage\Serialization\Serializer)->write($coverage, $data);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = dirname(__DIR__, 4);
$_SERVER['argv'][4] = $coverage;
$_SERVER['argv'][5] = __DIR__ . '/../../../fixture/example/patch';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($coverage);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  tests/fixture/example/src/Greeter.php:21
int(1)
