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
use function is_file;
use function printf;
use SebastianBergmann\CodeCoverage\Percentage;

final class PatchCoverageCommand extends Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_file($arguments->coverage())) {
            printf(
                'Code Coverage file "%s" does not exist' . PHP_EOL,
                $arguments->coverage()
            );

            return 1;
        }

        if (!is_file($arguments->patch())) {
            printf(
                'Patch file "%s" does not exist' . PHP_EOL,
                $arguments->patch()
            );

            return 1;
        }

        $pathPrefix = $arguments->pathPrefix() ?: '';

        $patchCoverage = (new PatchCoverage)->execute(
            $arguments->coverage(),
            $arguments->patch(),
            $pathPrefix,
            $arguments->mapFrom(),
            $arguments->mapTo()
        );

        if ($patchCoverage['numChangedLinesThatWereExecuted'] === 0 &&
            $patchCoverage['numChangedLinesThatAreExecutable'] === 0) {
            print 'Unable to detect executable lines that were changed.' . PHP_EOL;

            if ($pathPrefix === '') {
                print 'Are you sure you do not need to use --path-prefix?' . PHP_EOL;
            } else {
                print 'Are you sure your --path-prefix is correct?' . PHP_EOL;
            }

            return 1;
        }

        printf(
            '%d / %d changed executable lines covered (%s)' . PHP_EOL,
            $patchCoverage['numChangedLinesThatWereExecuted'],
            $patchCoverage['numChangedLinesThatAreExecutable'],
            Percentage::fromFractionAndTotal(
                $patchCoverage['numChangedLinesThatWereExecuted'],
                $patchCoverage['numChangedLinesThatAreExecutable']
            )->asFixedWidthString()
        );

        if (!empty($patchCoverage['changedLinesThatWereNotExecuted'])) {
            print PHP_EOL . 'Changed executable lines that are not covered:' . PHP_EOL;

            foreach ($patchCoverage['changedLinesThatWereNotExecuted'] as $file => $lines) {
                foreach ($lines as $line) {
                    printf(
                        '  %s:%d' . PHP_EOL,
                        $file,
                        $line
                    );
                }
            }

            return 1;
        }

        return 0;
    }
}
