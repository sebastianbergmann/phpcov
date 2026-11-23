--TEST--
phpcov patch-coverage <.cov file> (in a subdirectory of a repository that is configured to use an external diff tool, no prefixes, and relative paths)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$repository->git('config', 'diff.external', 'echo');
$repository->git('config', 'diff.noprefix', 'true');
$repository->git('config', 'diff.relative', 'true');
$repository->git('config', 'diff.renames', 'false');
$repository->git('config', 'color.ui', 'always');
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForName: 'Hi-Diddily-Ho, '));

chdir($repository->directory() . '/src');

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

0 / 1 changed executable lines covered (  0.00%)

Changed executable lines that are not covered:
  src/Greeter.php:13
int(1)
