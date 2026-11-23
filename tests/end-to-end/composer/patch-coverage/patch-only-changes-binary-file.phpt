--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov <patch that only changes a binary file>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$patch = tempnam(sys_get_temp_dir(), 'phpcov');

file_put_contents(
    $patch,
    <<<'EOT'
    diff --git a/logo.png b/logo.png
    index a251286..5fc5f26 100644
    Binary files a/logo.png and b/logo.png differ

    EOT,
);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][3] = $patch;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($patch);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Unable to detect executable lines that were changed.
Are you sure you do not need to use --path-prefix?
int(2)
