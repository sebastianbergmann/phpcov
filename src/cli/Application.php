<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCOV;

use const PHP_EOL;
use function dirname;
use function printf;
use SebastianBergmann\Version;

final class Application
{
    private const VERSION = '10.0.1';

    public function run(array $argv): int
    {
        $this->printVersion();

        try {
            $arguments = (new ArgumentsBuilder)->build($argv);
        } catch (Exception $e) {
            print PHP_EOL . $e->getMessage() . PHP_EOL;

            return 1;
        }

        if ($arguments->version()) {
            return 0;
        }

        print PHP_EOL;

        if ($arguments->help()) {
            return (new HelpCommand)->run($arguments);
        }

        if ($arguments->command() === 'execute') {
            return (new ExecuteCommand)->run($arguments);
        }

        if ($arguments->command() === 'merge') {
            return (new MergeCommand)->run($arguments);
        }

        if ($arguments->command() === 'patch-coverage') {
            return (new PatchCoverageCommand)->run($arguments);
        }

        (new HelpCommand)->run($arguments);

        return 1;
    }

    private function printVersion(): void
    {
        printf(
            'phpcov %s by Sebastian Bergmann.' . PHP_EOL,
            (new Version(self::VERSION, dirname(__DIR__)))->asString(),
        );
    }
}
