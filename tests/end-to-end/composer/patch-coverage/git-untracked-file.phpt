--TEST--
phpcov patch-coverage <.cov file> (file that is not tracked)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$repository->write('src/Farewell.php', GitRepository::greeter());

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []], 'Farewell.php' => [8 => [], 13 => ['a test']]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

1 / 2 changed executable lines covered ( 50.00%)

Changed executable lines that are not covered:
  src/Farewell.php:8 (Example\Greeter::greetWorld)
int(1)
