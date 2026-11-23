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
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;

/**
 * @phpstan-import-type LineCoverageType from ProcessedCodeCoverageData
 * @phpstan-import-type ChangedLinesType from PatchParser
 */
final class PatchCoverageCalculator
{
    /**
     * @param LineCoverageType $lineCoverage
     * @param ChangedLinesType $changedLines
     *
     * @return array{numChangedFilesWithCodeCoverageData: non-negative-int, numChangedLinesThatAreExecutable: non-negative-int, numChangedLinesThatWereExecuted: non-negative-int, changedLinesThatWereNotExecuted: array<non-empty-string, non-empty-list<positive-int>>}
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

            foreach ($lines as $line) {
                if (isset($lineCoverage[$key][$line]) &&
                    is_array($lineCoverage[$key][$line])) {
                    $result['numChangedLinesThatAreExecutable']++;

                    if ($lineCoverage[$key][$line] === []) {
                        if (!isset($result['changedLinesThatWereNotExecuted'][$file])) {
                            $result['changedLinesThatWereNotExecuted'][$file] = [];
                        }

                        $result['changedLinesThatWereNotExecuted'][$file][] = $line;
                    } else {
                        $result['numChangedLinesThatWereExecuted']++;
                    }
                }
            }
        }

        return $result;
    }
}
