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
use function is_array;
use function str_starts_with;
use function strlen;
use function substr;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Diff\Line;

/**
 * @phpstan-import-type LineCoverageType from ProcessedCodeCoverageData
 */
final class PatchCoverageCalculator
{
    /**
     * @param LineCoverageType $lineCoverage
     * @param list<Diff>       $patch
     *
     * @return array{numChangedLinesThatAreExecutable: non-negative-int, numChangedLinesThatWereExecuted: non-negative-int, changedLinesThatWereNotExecuted: array<non-empty-string, non-empty-list<positive-int>>}
     */
    public function calculate(array $lineCoverage, array $patch, string $basePath, string $pathPrefix): array
    {
        $result = [
            'numChangedLinesThatAreExecutable' => 0,
            'numChangedLinesThatWereExecuted'  => 0,
            'numChangedLinesNotCovered'        => 0,
            'changedLinesThatWereNotExecuted'  => [],
        ];

        if (substr($pathPrefix, -1, 1) !== DIRECTORY_SEPARATOR) {
            $pathPrefix .= DIRECTORY_SEPARATOR;
        }

        $changes = [];

        foreach ($patch as $diff) {
            $file           = substr($diff->to(), 2);
            $changes[$file] = [];

            foreach ($diff->chunks() as $chunk) {
                $lineNr = $chunk->end();

                foreach ($chunk->lines() as $line) {
                    if ($line->type() === Line::ADDED) {
                        $changes[$file][] = $lineNr;
                    }

                    if ($line->type() !== Line::REMOVED) {
                        $lineNr++;
                    }
                }
            }
        }

        foreach ($changes as $file => $lines) {
            $fullPath = $pathPrefix . $file;

            if ($basePath !== '' && str_starts_with($fullPath, $basePath . DIRECTORY_SEPARATOR)) {
                $key = substr($fullPath, strlen($basePath) + 1);
            } else {
                $key = $fullPath;
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
                if (!isset($coverage[$key][$line])) {
                    $result['numChangedLinesNotCovered']++;
                }
            }
        }

        return $result;
    }
}
