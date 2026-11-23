--TEST--
phpcov patch-coverage <.cov file> (Git is not available)
--FILE--
<?php declare(strict_types=1);
use SebastianBergmann\PHPCOV\Application;
use SebastianBergmann\PHPCOV\TestHelper\GitRepository;

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../../../_helper/GitRepository.php';

$repository = new GitRepository;

$repository->write('src/Greeter.php', GitRepository::greeter());
$repository->commit();
$path = getenv('PATH');

putenv('PATH=');

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

putenv('PATH=' . $path);

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Unable to determine the Git repository that "%s" belongs to: Git could not be run
int(255)
