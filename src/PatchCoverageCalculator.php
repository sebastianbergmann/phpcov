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
use function array_keys;
use function is_array;
use function is_file;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\StaticAnalysis\SourceLines;

/**
 * @phpstan-import-type LineCoverageType from ProcessedCodeCoverageData
 * @phpstan-import-type ChangedLinesType from PatchParser
 */
final class PatchCoverageCalculator
{
    /**
     * The changed lines that were not executed are grouped by file, and then
     * by the method or function they belong to. Lines that do not belong to
     * a method or function, and lines of a file that could not be read, are
     * grouped under an empty string.
     *
     * @param LineCoverageType $lineCoverage
     * @param ChangedLinesType $changedLines
     *
     * @return array{numChangedFilesWithCodeCoverageData: non-negative-int, numChangedLinesThatAreExecutable: non-negative-int, numChangedLinesThatWereExecuted: non-negative-int, changedLinesThatWereNotExecuted: array<string, array<string, non-empty-list<int>>>}
     */
    public function calculate(array $lineCoverage, array $changedLines, string $basePath, string $pathPrefix): array
    {
        $result = [
            'numChangedFilesWithCodeCoverageData' => 0,
            'numChangedLinesThatAreExecutable'    => 0,
            'numChangedLinesThatWereExecuted'     => 0,
            'changedLinesThatWereNotExecuted'     => [],
        ];

        if (substr($pathPrefix, -1, 1) !== DIRECTORY_SEPARATOR) {
            $pathPrefix .= DIRECTORY_SEPARATOR;
        }

        // The base path of code coverage data for source files that have
        // nothing but the root directory in common is the root directory,
        // which already ends with the directory separator
        if ($basePath !== '' && !str_ends_with($basePath, DIRECTORY_SEPARATOR)) {
            $basePath .= DIRECTORY_SEPARATOR;
        }

        foreach ($changedLines as $file => $lines) {
            $fullPath = $pathPrefix . str_replace('/', DIRECTORY_SEPARATOR, $file);

            if ($basePath !== '' && str_starts_with($fullPath, $basePath)) {
                $key = substr($fullPath, strlen($basePath));
            } else {
                $key = $fullPath;
            }

            if (!isset($lineCoverage[$key])) {
                continue;
            }

            $result['numChangedFilesWithCodeCoverageData']++;

            // Every line of a file is a changed line when the file is new
            if ($lines === null) {
                $lines = array_keys($lineCoverage[$key]);
            }

            $sourceLines = $this->sourceLines($fullPath);

            foreach ($lines as $line) {
                $executed = $this->wasExecuted($lineCoverage[$key], $line, $sourceLines);

                if ($executed === null) {
                    continue;
                }

                $result['numChangedLinesThatAreExecutable']++;

                if ($executed) {
                    $result['numChangedLinesThatWereExecuted']++;

                    continue;
                }

                $codeUnit = '';

                if ($sourceLines !== null) {
                    $codeUnit = $sourceLines->codeUnitAt($line) ?? '';
                }

                $result['changedLinesThatWereNotExecuted'][$file][$codeUnit][] = $line;
            }
        }

        return $result;
    }

    /**
     * Returns null for a line that is not executable.
     *
     * Which lines of a statement that spans multiple lines are executable
     * depends on how the executable lines were determined when the code
     * coverage data was collected: the line of an argument of a function
     * call, for instance, is not executable when they were determined from
     * the bytecode. Changing such a line changes the statement nevertheless.
     * A line that the code coverage data does not have as an executable line
     * is therefore treated as an executable line when it belongs to a
     * statement that has executable lines. It was executed when one of them
     * was executed.
     *
     * @param array<int, null|array<mixed>> $lineCoverage
     */
    private function wasExecuted(array $lineCoverage, int $line, ?SourceLines $sourceLines): ?bool
    {
        if (isset($lineCoverage[$line]) && is_array($lineCoverage[$line])) {
            return $lineCoverage[$line] !== [];
        }

        if ($sourceLines === null) {
            return null;
        }

        $executed = null;

        foreach ($sourceLines->linesOfStatementAt($line) as $lineOfStatement) {
            if (!isset($lineCoverage[$lineOfStatement]) || !is_array($lineCoverage[$lineOfStatement])) {
                continue;
            }

            if ($lineCoverage[$lineOfStatement] !== []) {
                return true;
            }

            $executed = false;
        }

        return $executed;
    }

    /**
     * The source code is not available when the code coverage data was
     * collected in another location, for instance.
     */
    private function sourceLines(string $file): ?SourceLines
    {
        if ($file === '' || !is_file($file)) {
            return null;
        }

        try {
            return SourceLines::of($file);
            // @codeCoverageIgnoreStart
        } catch (CodeCoverageException) {
            return null;
            // @codeCoverageIgnoreEnd
        }
    }
}
