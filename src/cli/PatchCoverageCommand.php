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
use function filemtime;
use function getcwd;
use function implode;
use function is_dir;
use function is_file;
use function ksort;
use function printf;
use function realpath;
use function sort;
use function sprintf;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function substr;
use DateTimeImmutable;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

/**
 * @phpstan-import-type ChangedLinesType from PatchParser
 */
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

        $patch = $arguments->patch();

        if ($patch !== null && !is_file($patch)) {
            printf(
                'Patch file "%s" does not exist' . PHP_EOL,
                $patch,
            );

            return 255;
        }

        try {
            $data = (new Unserializer)->unserialize($arguments->coverage());
            // @codeCoverageIgnoreStart
        } catch (CodeCoverageException $e) {
            print $e->getMessage() . PHP_EOL;

            return 255;
            // @codeCoverageIgnoreEnd
        }

        try {
            if ($patch !== null) {
                $pathPrefix   = $this->pathPrefix($arguments);
                $changedLines = (new PatchParser)->changedLines((string) file_get_contents($patch));
            } else {
                $git          = new Git($this->currentWorkingDirectory());
                $pathPrefix   = $git->repository();
                $changedLines = $this->changedLines($git, $arguments->base());
            }
        } catch (InvalidPatchException) {
            printf(
                'Patch file "%s" is not a unified diff' . PHP_EOL,
                $patch,
            );

            return 255;
        } catch (GitException $e) {
            print $e->getMessage() . PHP_EOL;

            return 255;
        }

        if ($patch === null && $arguments->requireUpToDateCodeCoverage()) {
            $files = $this->filesChangedAfterCodeCoverageDataWasWritten(
                array_keys($changedLines),
                $pathPrefix,
                $data['basePath'],
                $this->timeCodeCoverageDataWasWritten($data['buildInformation']['timestamp'], $arguments->coverage()),
            );

            if ($files !== []) {
                print 'The code coverage data is older than the changes to these files:' . PHP_EOL;

                foreach ($files as $file) {
                    print '  ' . $file . PHP_EOL;
                }

                print PHP_EOL . 'Collect code coverage again, or use --do-not-require-up-to-date-code-coverage.' . PHP_EOL;

                return 2;
            }
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

            if ($patch === null) {
                print 'Was the code coverage data collected in this Git repository?' . PHP_EOL;
            } elseif ($arguments->pathPrefix() === null) {
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

            foreach ($patchCoverage['changedLinesThatWereNotExecuted'] as $file => $codeUnits) {
                foreach ($codeUnits as $codeUnit => $lines) {
                    printf(
                        '  %s:%s%s' . PHP_EOL,
                        $file,
                        implode(', ', $this->ranges($lines)),
                        $codeUnit !== '' ? ' (' . $codeUnit . ')' : '',
                    );
                }
            }

            return 1;
        }

        return 0;
    }

    /**
     * The lines that were added or changed in the working tree of a Git
     * repository, compared to the commit that HEAD and a revision have in
     * common. Every line of a file that is not tracked yet is such a line.
     *
     * @throws GitException
     * @throws InvalidPatchException
     *
     * @return ChangedLinesType
     */
    private function changedLines(Git $git, ?string $revision): array
    {
        if ($revision === null) {
            $revision = $git->defaultRevision();
        }

        $commit      = 'HEAD';
        $description = 'HEAD';

        if ($revision !== 'HEAD') {
            $commit      = $git->mergeBase($revision);
            $description = sprintf(
                '%s (merge base %s)',
                $revision,
                substr($commit, 0, 12),
            );
        }

        $changedLines = (new PatchParser)->changedLines($git->diff($commit));

        foreach ($git->untrackedFiles() as $file) {
            $changedLines[$file] = null;
        }

        ksort($changedLines);

        printf(
            'Changes compared to %s, including uncommitted changes and untracked files' . PHP_EOL . PHP_EOL,
            $description,
        );

        return $changedLines;
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
            return $this->currentWorkingDirectory();
        }

        $resolved = realpath($pathPrefix);

        if ($resolved !== false) {
            return $resolved;
        }

        return $pathPrefix;
    }

    private function currentWorkingDirectory(): string
    {
        $directory = getcwd();

        if ($directory === false) {
            // @codeCoverageIgnoreStart
            return '';
            // @codeCoverageIgnoreEnd
        }

        return $directory;
    }

    /**
     * Code coverage data does not describe a file that was changed after the
     * data was written: lines that were added since are not part of it, and
     * the lines that it has data for may have moved.
     *
     * Only the files below the base path of the code coverage data are
     * considered, as only these can be files that code coverage is collected
     * for. A file that was added after the data was written is not part of
     * it and would otherwise go unnoticed.
     *
     * @param list<string> $files
     *
     * @return list<string>
     */
    private function filesChangedAfterCodeCoverageDataWasWritten(array $files, string $pathPrefix, string $basePath, int $time): array
    {
        if ($basePath === '') {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        if (!str_ends_with($basePath, DIRECTORY_SEPARATOR)) {
            $basePath .= DIRECTORY_SEPARATOR;
        }

        $result = [];

        foreach ($files as $file) {
            $path = $pathPrefix . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);

            if (!str_starts_with($path, $basePath) || !is_file($path)) {
                continue;
            }

            if (filemtime($path) > $time) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * The time that is stored in the code coverage data has a time zone
     * abbreviation that may not be understood. The time the file was last
     * modified is used then: it is not earlier than the time the data was
     * written, but it is later when the file was copied, for instance.
     */
    private function timeCodeCoverageDataWasWritten(string $timestamp, string $file): int
    {
        $time = DateTimeImmutable::createFromFormat('D M j G:i:s T Y', $timestamp);

        if ($time !== false) {
            return $time->getTimestamp();
        }

        return (int) filemtime($file);
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

    /**
     * @param non-empty-list<int> $lines
     *
     * @return non-empty-list<non-empty-string>
     */
    private function ranges(array $lines): array
    {
        sort($lines);

        $ranges = [];
        $start  = $lines[0];
        $end    = $lines[0];

        foreach ($lines as $line) {
            if ($line > $end + 1) {
                $ranges[] = $this->range($start, $end);
                $start    = $line;
            }

            $end = $line;
        }

        $ranges[] = $this->range($start, $end);

        return $ranges;
    }

    /**
     * @return non-empty-string
     */
    private function range(int $start, int $end): string
    {
        if ($start === $end) {
            return (string) $start;
        }

        return $start . '-' . $end;
    }

    private function percentage(float $fraction, float $total): string
    {
        if ($total > 0) {
            return sprintf('%6.2F%%', ($fraction / $total) * 100);
        }

        return '';
    }
}
