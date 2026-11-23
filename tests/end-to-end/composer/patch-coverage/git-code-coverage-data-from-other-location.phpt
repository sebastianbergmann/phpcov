--TEST--
phpcov patch-coverage <.cov file that was written in another location>
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$repository->write('src/Greeter.php', GitRepository::greeter(greetingForName: 'Hi-Diddily-Ho, '));

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]], DIRECTORY_SEPARATOR . 'does' . DIRECTORY_SEPARATOR . 'not' . DIRECTORY_SEPARATOR . 'exist');

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

Unable to find the files that the patch changes.
The paths in the patch were resolved against "%s", the code coverage data is for files in "%s".
Was the code coverage data collected in this Git repository?
int(2)
