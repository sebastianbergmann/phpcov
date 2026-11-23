--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov <patch that changes multiple lines that are not covered>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$data = (new SebastianBergmann\CodeCoverage\Serialization\Unserializer)->unserialize(
    __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov',
);

$file     = $data['codeCoverage']->coveredFiles()[0];
$basePath = $data['basePath'];

$data['codeCoverage'] = new SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;

$data['codeCoverage']->setLineCoverage(
    [
        $file => [
            16 => [],
            20 => [],
            21 => [],
            22 => [],
            24 => ['a test'],
            26 => [],
        ],
    ],
);

$coverage = tempnam(sys_get_temp_dir(), 'phpcov');

(new SebastianBergmann\CodeCoverage\Serialization\Serializer)->write($coverage, $data);

$patch = tempnam(sys_get_temp_dir(), 'phpcov');

file_put_contents(
    $patch,
    <<<'EOT'
    diff --git a/src/Greeter.php b/src/Greeter.php
    index a251286..5fc5f26 100644
    --- a/src/Greeter.php
    +++ b/src/Greeter.php
    @@ -15,0 +16 @@
    +16
    @@ -18,0 +20,7 @@
    +20
    +21
    +22
    +23
    +24
    +25
    +26

    EOT,
);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = dirname($basePath);
$_SERVER['argv'][4] = $coverage;
$_SERVER['argv'][5] = $patch;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($coverage);
unlink($patch);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

1 / 6 changed executable lines covered ( 16.67%)

Changed executable lines that are not covered:
  src/Greeter.php:16, 20-22, 26
int(1)
