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
use function serialize;
use SebastianBergmann\CodeCoverage\Serialization\DriverMismatchException;
use SebastianBergmann\CodeCoverage\Serialization\EmptyPathListException;
use SebastianBergmann\CodeCoverage\Serialization\FileCouldNotBeReadException;
use SebastianBergmann\CodeCoverage\Serialization\GitInformationMismatchException;
use SebastianBergmann\CodeCoverage\Serialization\InvalidCoverageDataException;
use SebastianBergmann\CodeCoverage\Serialization\Merger as CoverageMerger;
use SebastianBergmann\CodeCoverage\Serialization\MixedGitInformationException;
use SebastianBergmann\CodeCoverage\Serialization\RuntimeMismatchException;
use SebastianBergmann\CodeCoverage\Serialization\VersionMismatchException;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Version;
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
        } catch (DriverMismatchException|GitInformationMismatchException|MixedGitInformationException|RuntimeMismatchException $e) {
            print $e->getMessage() . PHP_EOL;

            return 1;
        } catch (FileCouldNotBeReadException|InvalidCoverageDataException|VersionMismatchException $e) {
            print 'Failed to merge: ' . $e->getMessage() . PHP_EOL;

            return 1;
        } catch (EmptyPathListException $e) {
            return 1;
        }

        if ($arguments->php() !== null) {
            print 'Generating code coverage report in PHP format ... ';

            Filesystem::write(
                $arguments->php(),
                '<?php // phpunit/php-code-coverage version ' . Version::id() . PHP_EOL .
                "return \unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'" . PHP_EOL .
                serialize($merged) . PHP_EOL .
                'END_OF_COVERAGE_SERIALIZATION' . PHP_EOL .
                ');',
            );

            print 'done' . PHP_EOL;
        }

        $this->handleReports($merged['codeCoverage'], $merged['testResults'], $arguments);

        return 0;
    }
}
