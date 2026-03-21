<?php declare(strict_types=1);

/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require __DIR__ . '/../../vendor/autoload.php';

use SebastianBergmann\CodeCoverage\Serialization\Unserializer;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Version;

$unserializer = new Unserializer;

$world = $unserializer->unserialize(__DIR__ . '/example/coverage/testGreetsWorld.cov');
$name  = $unserializer->unserialize(__DIR__ . '/example/coverage/testGreetsWithName.cov');

$write = static function (string $path, array $data): void
{
    Filesystem::write(
        $path,
        '<?php // phpunit/php-code-coverage version ' . Version::id() . PHP_EOL .
        "return \unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'" . PHP_EOL .
        \serialize($data) . PHP_EOL .
        'END_OF_COVERAGE_SERIALIZATION' . PHP_EOL .
        ');',
    );
};

// Mismatching driver: testGreetsWorld keeps original, testGreetsWithName gets different driver
$driverName                                                             = $name;
$driverName['buildInformation']['phpCodeCoverage']['driverInformation'] = [
    'name'    => 'PCOV',
    'version' => '1.0.0',
];
$write(__DIR__ . '/mismatching-driver/coverage/testGreetsWorld.cov', $world);
$write(__DIR__ . '/mismatching-driver/coverage/testGreetsWithName.cov', $driverName);

// Mismatching git: both get git info but with different commits
$gitWorld                            = $world;
$gitWorld['buildInformation']['git'] = [
    'originUrl' => 'https://example.com/repo.git',
    'branch'    => 'main',
    'commit'    => 'abc123',
    'status'    => '',
    'isClean'   => true,
];
$gitName                            = $name;
$gitName['buildInformation']['git'] = [
    'originUrl' => 'https://example.com/repo.git',
    'branch'    => 'feature',
    'commit'    => 'def456',
    'status'    => '',
    'isClean'   => true,
];
$write(__DIR__ . '/mismatching-git/coverage/testGreetsWorld.cov', $gitWorld);
$write(__DIR__ . '/mismatching-git/coverage/testGreetsWithName.cov', $gitName);

// Mismatching PHP version: testGreetsWorld keeps original, testGreetsWithName gets different version
$phpName                                           = $name;
$phpName['buildInformation']['runtime']['version'] = '8.4.0';
$write(__DIR__ . '/mismatching-php-version/coverage/testGreetsWorld.cov', $world);
$write(__DIR__ . '/mismatching-php-version/coverage/testGreetsWithName.cov', $phpName);

print 'Generated mismatching fixtures' . PHP_EOL;
