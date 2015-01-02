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
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/php-code-coverage/tree
 * @since     Class available since Release 2.0.0
 */
class PatchCoverageTest extends PHPUnit_Framework_TestCase
{
    public function testPatchCoverageIsCalculatedCorrectly()
    {
        $pc = new PatchCoverage;

        $this->assertEquals(
            array(
                'numChangedLinesThatAreExecutable' => 2,
                'numChangedLinesThatWereExecuted' => 1,
                'changedLinesThatWereNotExecuted' => array(
                    'Example.php' => array(11)
                )
            ),
            $pc->execute(
                __DIR__ . '/fixture/coverage.php',
                __DIR__ . '/fixture/patch.txt',
                '/tmp/example/'
            )
        );
    }
}
