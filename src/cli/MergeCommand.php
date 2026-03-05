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
use function is_dir;
use function printf;
use function realpath;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Serialization\Merger as CoverageMerger;
use SebastianBergmann\FileIterator\Facade;

final class MergeCommand extends Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_dir($arguments->directory())) {
            printf(
                '"%s" is not a directory' . PHP_EOL,
                $arguments->directory(),
            );

            return 1;
        }

        if (!$arguments->reportConfigured()) {
            print 'No code coverage report configured' . PHP_EOL;

            return 1;
        }

        $files = (new Facade)->getFilesAsArray(
            $arguments->directory(),
            ['.cov'],
        );

        if ($files === []) {
            printf(
                'No "%s/*.cov" files found' . PHP_EOL,
                realpath($arguments->directory()),
            );

            return 1;
        }

        try {
            $merged = (new CoverageMerger)->merge($files);
        } catch (CodeCoverageException $e) {
            print $e->getMessage() . PHP_EOL;

            return 1;
        }

        $this->handleReports($merged['codeCoverage'], $merged['testResults'], $arguments);

        return 0;
    }
}
