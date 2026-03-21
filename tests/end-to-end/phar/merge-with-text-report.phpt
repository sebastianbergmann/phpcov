--TEST--
phpcov merge --text php://stdout (PHAR)
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
$phar = dirname(__DIR__, 3) . '/build/artifacts/phpcov-snapshot.phar';
print shell_exec('php ' . $phar . ' merge --text php://stdout ' . __DIR__ . '/../../fixture/example/coverage');
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in text format ...%s

Code Coverage Report:
  %s

 Summary:
  Classes: 100.00% (1/1)
  Methods: 100.00% (2/2)
  Lines:   66.67% (2/3)

SebastianBergmann\PHPCOV\TestFixture\Greeter
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  2/  2)
done
