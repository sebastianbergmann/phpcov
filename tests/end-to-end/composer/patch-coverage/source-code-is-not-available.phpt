--TEST--
phpcov patch-coverage --path-prefix /does/not/exist <.cov file that was written in another location> <patch> (the method that a line belongs to is not known without the source code)
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

$data['basePath'] = '/does/not/exist/src';

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
    @@ -21 +21 @@
    -        return 'Greetings, ' . $name;
    +        return 'Hi-Diddily-Ho, ' . $name;

    EOT,
);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--path-prefix';
$_SERVER['argv'][3] = '/does/not/exist';
$_SERVER['argv'][4] = $coverage;
$_SERVER['argv'][5] = $patch;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($coverage);
unlink($patch);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  src/Greeter.php:21
int(1)
