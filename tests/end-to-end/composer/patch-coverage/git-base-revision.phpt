--TEST--
phpcov patch-coverage --base main <.cov file> (changes of main since the branch was created are not changes of the branch)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$repository->git('switch', '--quiet', '--create', 'feature');
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForWorld: 'Hello wonderful world!'));
$repository->commit();
$repository->git('switch', '--quiet', 'main');
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForName: 'Hi-Diddily-Ho, '));
$repository->commit();
$repository->git('switch', '--quiet', 'feature');

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--base';
$_SERVER['argv'][3] = 'main';
$_SERVER['argv'][4] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to main (merge base %s), including uncommitted changes and untracked files

1 / 1 changed executable lines covered (100.00%)
int(0)
