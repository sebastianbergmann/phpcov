--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov <patch that does not change files that code coverage data is available for>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

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

chdir(dirname(__DIR__, 4));

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][3] = $patch;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($patch);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

The patch does not change files that code coverage data is available for.
int(0)
