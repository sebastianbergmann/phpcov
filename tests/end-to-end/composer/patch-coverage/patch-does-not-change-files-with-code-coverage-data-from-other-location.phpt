--TEST--
phpcov patch-coverage --path-prefix /does/not/exist <.cov file that was written in another location> <patch that does not change files that code coverage data is available for>
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
    diff --git a/README.md b/README.md
    index a251286..5fc5f26 100644
    --- a/README.md
    +++ b/README.md
    @@ -1 +1 @@
    -# phpcov
    +# PHPCOV

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

The patch does not change files that code coverage data is available for.
int(0)
