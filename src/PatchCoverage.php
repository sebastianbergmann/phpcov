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
 * @since     Class available since Release 2.0.0
 */
class PatchCoverage
{
    /**
     * @param  string $coverage
     * @param  string $patch
     * @param  string $prefix
     * @return array
     */
    public function execute($coverage, $patch, $prefix)
    {
        $result = array(
            'numChangedLinesThatAreExecutable' => 0,
            'numChangedLinesThatWereExecuted'  => 0,
            'changedLinesThatWereNotExecuted'  => array()
        );

        $coverage = include($coverage);
        $coverage = $coverage->getData();
        $parser   = new DiffParser;
        $patch    = $parser->parse(file_get_contents($patch));
        $changes  = array();

        foreach ($patch as $diff) {
            $file           = substr($diff->getFrom(), 2);
            $changes[$file] = array();

            foreach ($diff->getChunks() as $chunk) {
                $lineNr = $chunk->getStart();

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
                            $result['changedLinesThatWereNotExecuted'][$file] = array();
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
