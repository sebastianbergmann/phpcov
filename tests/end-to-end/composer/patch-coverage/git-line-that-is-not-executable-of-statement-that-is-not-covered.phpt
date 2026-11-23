--TEST--
phpcov patch-coverage <.cov file> (change to a line that is not executable of a statement that is not covered)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Farewell.php', GitRepository::farewell());
$repository->commit();
$repository->write('src/Farewell.php', GitRepository::farewell('Goodbye'));

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Farewell.php' => [8 => [], 9 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  src/Farewell.php:10 (Example\Farewell::bye)
int(1)
