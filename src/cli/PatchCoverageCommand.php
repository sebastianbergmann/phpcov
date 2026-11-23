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

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use function array_any;
use function array_keys;
use function file_get_contents;
use function getcwd;
use function is_dir;
use function is_file;
use function printf;
use function realpath;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

final class PatchCoverageCommand implements Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_file($arguments->coverage())) {
            printf(
                'Code Coverage file "%s" does not exist' . PHP_EOL,
                $arguments->coverage(),
            );

            return 255;
        }

        if (!is_file($arguments->patch())) {
            printf(
                'Patch file "%s" does not exist' . PHP_EOL,
                $arguments->patch(),
            );

            return 255;
        }

        $pathPrefix = $this->pathPrefix($arguments);

        try {
            $data = (new Unserializer)->unserialize($arguments->coverage());
            // @codeCoverageIgnoreStart
        } catch (CodeCoverageException $e) {
            print $e->getMessage() . PHP_EOL;

            return 255;
            // @codeCoverageIgnoreEnd
        }

        try {
            $changedLines = (new PatchParser)->changedLines((string) file_get_contents($arguments->patch()));
        } catch (InvalidPatchException) {
            printf(
                'Patch file "%s" is not a unified diff' . PHP_EOL,
                $arguments->patch(),
            );

            return 255;
        }

        $patchCoverage = (new PatchCoverageCalculator)->calculate(
            $data['codeCoverage']->lineCoverage(),
            $changedLines,
            $data['basePath'],
            $pathPrefix,
        );

        if ($changedLines !== [] &&
            $patchCoverage['numChangedFilesWithCodeCoverageData'] === 0 &&
            !$this->pathsCanBeMapped(array_keys($changedLines), $data['basePath'], $pathPrefix)) {
            print 'Unable to find the files that the patch changes.' . PHP_EOL;

            printf(
                'The paths in the patch were resolved against "%s", the code coverage data is for files in "%s".' . PHP_EOL,
                $pathPrefix,
                $data['basePath'],
            );

            if ($arguments->pathPrefix() === null) {
                print 'Are you sure you do not need to use --path-prefix?' . PHP_EOL;
            } else {
                print 'Are you sure your --path-prefix is correct?' . PHP_EOL;
            }

            return 2;
        }

        if ($patchCoverage['numChangedFilesWithCodeCoverageData'] === 0) {
            print 'The patch does not change files that code coverage data is available for.' . PHP_EOL;

            return 0;
        }

        if ($patchCoverage['numChangedLinesThatAreExecutable'] === 0) {
            print 'The patch does not change executable lines.' . PHP_EOL;

            return 0;
        }

        printf(
            '%d / %d changed executable lines covered (%s)' . PHP_EOL,
            $patchCoverage['numChangedLinesThatWereExecuted'],
            $patchCoverage['numChangedLinesThatAreExecutable'],
            $this->percentage(
                $patchCoverage['numChangedLinesThatWereExecuted'],
                $patchCoverage['numChangedLinesThatAreExecutable'],
            ),
        );

        if ($patchCoverage['changedLinesThatWereNotExecuted'] !== []) {
            print PHP_EOL . 'Changed executable lines that are not covered:' . PHP_EOL;

            foreach ($patchCoverage['changedLinesThatWereNotExecuted'] as $file => $lines) {
                foreach ($lines as $line) {
                    printf(
                        '  %s:%d' . PHP_EOL,
                        $file,
                        $line,
                    );
                }
            }

            return 1;
        }

        return 0;
    }

    /**
     * The paths in a patch are relative to the directory the patch was
     * generated for. Unless configured otherwise, this is expected to be
     * the current working directory.
     */
    private function pathPrefix(Arguments $arguments): string
    {
        $pathPrefix = $arguments->pathPrefix();

        if ($pathPrefix === null) {
            $pathPrefix = getcwd();

            if ($pathPrefix === false) {
                // @codeCoverageIgnoreStart
                return '';
                // @codeCoverageIgnoreEnd
            }

            return $pathPrefix;
        }

        $resolved = realpath($pathPrefix);

        if ($resolved !== false) {
            return $resolved;
        }

        return $pathPrefix;
    }

    /**
     * Tells apart a patch that does not change files that code coverage data
     * is available for from a patch whose paths were resolved against the
     * wrong directory. In both cases, none of the files that the patch
     * changes is found in the code coverage data.
     *
     * @param list<string> $files
     */
    private function pathsCanBeMapped(array $files, string $basePath, string $pathPrefix): bool
    {
        if (!str_ends_with($pathPrefix, DIRECTORY_SEPARATOR)) {
            $pathPrefix .= DIRECTORY_SEPARATOR;
        }

        if ($basePath !== '') {
            if (!str_ends_with($basePath, DIRECTORY_SEPARATOR)) {
                $basePath .= DIRECTORY_SEPARATOR;
            }

            if (!str_starts_with($basePath, $pathPrefix) && !str_starts_with($pathPrefix, $basePath)) {
                return false;
            }

            // The code coverage data was collected in another location,
            // the files that the patch changes cannot be expected to exist
            if (!is_dir($basePath)) {
                return true;
            }
        }

        return array_any(
            $files,
            static fn (string $file): bool => is_file($pathPrefix . $file),
        );
    }

    private function percentage(float $fraction, float $total): string
    {
        if ($total > 0) {
            return sprintf('%6.2F%%', ($fraction / $total) * 100);
        }

        return '';
    }
}
