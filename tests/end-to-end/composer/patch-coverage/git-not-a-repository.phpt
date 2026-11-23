--TEST--
phpcov patch-coverage <.cov file> (in a directory that does not belong to a Git repository)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('phpcov');

mkdir($directory);

chdir($directory);

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

rmdir($directory);

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Unable to determine the Git repository that "%s" belongs to: %s
int(255)
