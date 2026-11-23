--TEST--
phpcov patch-coverage <.cov file> (only the lines that were changed in a file that was renamed are changed lines)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
rename($repository->directory() . '/src/Greeter.php', $repository->directory() . '/src/Welcomer.php');
$repository->write('src/Welcomer.php', GitRepository::greeter(greetingForWorld: 'Hello wonderful world!'));
$repository->git('add', '--all');

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Welcomer.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

1 / 1 changed executable lines covered (100.00%)
int(0)
