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

use function preg_match;
use function substr;
use function trim;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;

/**
 * @phpstan-type ChangedLinesType array<string, list<int>>
 */
final class PatchParser
{
    /**
     * Returns the lines that a patch adds or changes, by file. The paths of
     * the files are relative to the directory the patch was generated for.
     *
     * @throws InvalidPatchException
     *
     * @return ChangedLinesType
     */
    public function changedLines(string $patch): array
    {
        if (trim($patch) === '') {
            return [];
        }

        $diffs = (new DiffParser)->parse($patch);

        // A patch that only changes binary files, or the mode of files, has
        // no lines that were changed
        if ($diffs === [] && preg_match('/^diff /m', $patch) !== 1) {
            throw new InvalidPatchException;
        }

        $changedLines = [];

        foreach ($diffs as $diff) {
            if ($diff->to() === '/dev/null') {
                continue;
            }

            $file                = substr($diff->to(), 2);
            $changedLines[$file] = [];

            foreach ($diff->chunks() as $chunk) {
                $lineNr = $chunk->end();

                foreach ($chunk->lines() as $line) {
                    if ($line->type() === Line::ADDED) {
                        $changedLines[$file][] = $lineNr;
                    }

                    if ($line->type() !== Line::REMOVED) {
                        $lineNr++;
                    }
                }
            }
        }

        return $changedLines;
    }
}
