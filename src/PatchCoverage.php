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
use function assert;
use function file_get_contents;
use function is_array;
use function substr;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

final class PatchCoverage
{
    public function execute(string $coverageFile, string $patchFile, string $pathPrefix): array
    {
        $result = [
            'numChangedLinesThatAreExecutable' => 0,
            'numChangedLinesThatWereExecuted'  => 0,
            'changedLinesThatWereNotExecuted'  => [],
        ];

        if (substr($pathPrefix, -1, 1) !== DIRECTORY_SEPARATOR) {
            $pathPrefix .= DIRECTORY_SEPARATOR;
        }

        $coverage = include $coverageFile;

        assert($coverage instanceof CodeCoverage);

        $coverage = $coverage->getData()->lineCoverage();
        $patch    = (new DiffParser)->parse(file_get_contents($patchFile));
        $changes  = [];

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
            $key = $pathPrefix . $file;

            foreach ($lines as $line) {
                if (isset($coverage[$key][$line]) &&
                    is_array($coverage[$key][$line])) {
                    $result['numChangedLinesThatAreExecutable']++;

                    if (empty($coverage[$key][$line])) {
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
