<?php
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCOV;

use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

/**
 * @since Class available since Release 2.0.0
 */
class PatchCoverage
{
    /**
     * @param string $coverage
     * @param string $patch
     * @param string $prefix
     *
     * @return array
     */
    public function execute($coverage, $patch, $prefix)
    {
        $result = [
            'numChangedLinesThatAreExecutable' => 0,
            'numChangedLinesThatWereExecuted'  => 0,
            'changedLinesThatWereNotExecuted'  => []
        ];

        if (substr($prefix, -1, 1) != DIRECTORY_SEPARATOR) {
            $prefix .= DIRECTORY_SEPARATOR;
        }

        $coverage = include($coverage);
        $coverage = $coverage->getData();
        $parser   = new DiffParser;
        $patch    = $parser->parse(file_get_contents($patch));
        $changes  = [];

        foreach ($patch as $diff) {
            $file           = substr($diff->getTo(), 2);
            $changes[$file] = [];

            foreach ($diff->getChunks() as $chunk) {
                $lineNr = $chunk->getEnd();

                foreach ($chunk->getLines() as $line) {
                    if ($line->getType() == Line::ADDED) {
                        $changes[$file][] = $lineNr;
                    }

                    if ($line->getType() != Line::REMOVED) {
                        $lineNr++;
                    }
                }
            }
        }

        foreach ($changes as $file => $lines) {
            $key = $prefix . $file;

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
