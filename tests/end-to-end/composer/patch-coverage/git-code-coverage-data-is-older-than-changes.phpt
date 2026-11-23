--TEST--
phpcov patch-coverage <.cov file that is older than the changes>
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
$repository->write('src/Farewell.php', GitRepository::greeter());
$repository->write('README.md', 'README');
$repository->touch('src/Greeter.php', 60);
$repository->touch('src/Farewell.php', 60);
$repository->touch('README.md', 60);

chdir($repository->directory());

$_SERVER['argv'][1] = 'patch-coverage';
$_SERVER['argv'][2] = $repository->coverage(['Greeter.php' => [8 => ['a test'], 13 => []]]);

var_dump((new Application)->run($_SERVER['argv']));

$repository->delete();
--EXPECTF--
phpcov %s by Sebastian Bergmann.

Changes compared to HEAD, including uncommitted changes and untracked files

The code coverage data is older than the changes to these files:
  src/Farewell.php
  src/Greeter.php

Collect code coverage again, or use --do-not-require-up-to-date-code-coverage.
int(2)
