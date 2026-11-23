--TEST--
phpcov patch-coverage <.cov file> (no changes)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

The patch does not change files that code coverage data is available for.
int(0)
