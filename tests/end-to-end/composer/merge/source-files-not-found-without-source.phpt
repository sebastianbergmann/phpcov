--TEST--
phpcov merge --text /tmp/file <directory with .cov file for source files that do not exist>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$data = (new SebastianBergmann\CodeCoverage\Serialization\Unserializer)->unserialize(
    __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov',
);

$lineCoverage = [];

for ($i = 1; $i <= 12; $i++) {
    $lineCoverage[sprintf('File%02d.php', $i)] = [1 => []];
}

$data['basePath'] = '/does/not/exist';
$data['codeCoverage']->setLineCoverage($lineCoverage);
$data['codeCoverage']->setFunctionCoverage([]);

$covDir = sys_get_temp_dir() . '/phpcov_merge_' . uniqid();
mkdir($covDir);

(new SebastianBergmann\CodeCoverage\Serialization\Serializer)->write($covDir . '/coverage.cov', $data);

$tmp = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--text';
$_SERVER['argv'][3] = $tmp;
$_SERVER['argv'][4] = $covDir;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($tmp);
unlink($covDir . '/coverage.cov');
rmdir($covDir);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Warning: The code coverage data of 12 files is not part of the reports because these files were not found:

  /does/not/exist/File01.php
  /does/not/exist/File02.php
  /does/not/exist/File03.php
  /does/not/exist/File04.php
  /does/not/exist/File05.php
  /does/not/exist/File06.php
  /does/not/exist/File07.php
  /does/not/exist/File08.php
  /does/not/exist/File09.php
  /does/not/exist/File10.php
  (and 2 more)

The base path of the merged code coverage data is "/does/not/exist". Use --source when the source code is located somewhere else.

Generating code coverage report in text format ... done
int(0)
