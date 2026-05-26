--TEST--
phpcov --version (PHAR)
--FILE--
<?php declare(strict_types=1);
$phar = dirname(__DIR__, 3) . '/build/artifacts/phpcov-snapshot.phar';
print shell_exec('php ' . $phar . ' --version');
--EXPECTF--
phpcov %s by Sebastian Bergmann.
