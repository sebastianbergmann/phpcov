--TEST--
phpcov merge --text php://stdout ../../fixture/example/coverage
--INI--
xdebug.overload_var_dump=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../../vendor/autoload.php';

$_SERVER['argv'][1] = 'merge';
$_SERVER['argv'][2] = '--text';
$_SERVER['argv'][3] = 'php://stdout';
$_SERVER['argv'][4] = __DIR__ . '/../../fixture/example/coverage';

var_dump((new SebastianBergmann\PHPCOV\Application)->run($_SERVER['argv']));
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Generating code coverage report in text format ... 

Code Coverage Report:   
  %s   
                        
 Summary:               
  Classes: 100.00% (1/1)
  Methods: 100.00% (2/2)
  Lines:   66.67% (2/3) 

SebastianBergmann\PHPCOV\TestFixture\Greeter
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  2/  2)
