--TEST--
phpcov patch-coverage <.cov file> (change to a line of a statement that has no executable lines)
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
$_SERVER['argv'][2] = $repository->coverage(['Farewell.php' => [6 => [], 8 => null]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

The patch does not change executable lines.
int(0)
