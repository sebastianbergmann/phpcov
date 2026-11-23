--TEST--
phpcov patch-coverage <.cov file> (committed changes are compared to the default branch of the origin remote)
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
$repository->git('switch', '--quiet', '--create', 'feature');
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForName: 'Hi-Diddily-Ho, '));
$repository->commit();
$repository->write('src/Greeter.php', GitRepository::greeter('Hello wonderful world!', 'Hi-Diddily-Ho, '));

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to origin/main (merge base %s), including uncommitted changes and untracked files

1 / 2 changed executable lines covered ( 50.00%)

Changed executable lines that are not covered:
  src/Greeter.php:13
int(1)
