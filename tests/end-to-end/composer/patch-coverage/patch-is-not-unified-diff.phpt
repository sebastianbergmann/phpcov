--TEST--
phpcov patch-coverage ../../fixture/example/coverage/testGreetsWorld.cov <file that is not a unified diff>
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../../vendor/autoload.php';

$patch = tempnam(sys_get_temp_dir(), 'phpcov');

file_put_contents(
    $patch,
    <<<'EOT'
    src/Greeter.php --- PHP
    19     public function greetWithName(string $name): string
    20     {
    21         return 'Greetings, ' . $name;
       21         return 'Hi-Diddily-Ho, ' . $name;
    22     }

    EOT,
);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = __DIR__ . '/../../../fixture/example/coverage/testGreetsWorld.cov';
$_SERVER['argv'][3] = $patch;

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));

unlink($patch);
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Patch file "%s" is not a unified diff
int(255)
