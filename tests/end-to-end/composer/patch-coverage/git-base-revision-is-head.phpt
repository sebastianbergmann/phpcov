--TEST--
phpcov patch-coverage --base HEAD <.cov file> (committed changes are not compared)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$repository->git('update-ref', 'refs/remotes/origin/main', 'HEAD');
$repository->git('symbolic-ref', 'refs/remotes/origin/HEAD', 'refs/remotes/origin/main');
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForName: 'Hi-Diddily-Ho, '));
$repository->commit();
$repository->write('src/Greeter.php', GitRepository::greeter('Hello wonderful world!', 'Hi-Diddily-Ho, '));

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = '--base';
$_SERVER['argv'][3] = 'HEAD';
$_SERVER['argv'][4] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

1 / 1 changed executable lines covered (100.00%)
int(0)
