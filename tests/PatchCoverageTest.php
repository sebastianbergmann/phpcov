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

use PHPUnit_Framework_TestCase;

/**
 * @since     Class available since Release 2.0.0
 */
class PatchCoverageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerForPatchCoverageIsCalculatedCorrectly
     */
    public function testPatchCoverageIsCalculatedCorrectly($patchFile)
    {
        $pc = new PatchCoverage;

        $this->assertEquals(
            [
                'numChangedLinesThatAreExecutable' => 2,
                'numChangedLinesThatWereExecuted'  => 1,
                'changedLinesThatWereNotExecuted'  => [
                    'Example.php' => [11]
                ]
            ],
            $pc->execute(
                __DIR__ . '/fixture/coverage.php',
                __DIR__ . '/fixture/' . $patchFile . '.txt',
                '/tmp/example/'
            )
        );
    }

    public function providerForPatchCoverageIsCalculatedCorrectly()
    {
        return [
            // Original patch
            [
                'patch'
            ],
            // Patch showing a renamed file
            [
                'patch2'
            ],
            // Patch with different start and end numbers
            [
                'patch3'
            ]
        ];
    }
}
