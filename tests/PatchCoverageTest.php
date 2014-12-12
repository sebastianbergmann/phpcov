<?php
/**
 * phpcov
 *
 * Copyright (c) 2011-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcov
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @author    Rob Caiger <rob@clocal.co.uk>
 * @copyright 2011-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 2.0.0
 */

namespace SebastianBergmann\PHPCOV;

use PHPUnit_Framework_TestCase;

/**
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @author    Rob Caiger <rob@clocal.co.uk>
 * @copyright 2011-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/php-code-coverage/tree
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
            array(
                'numChangedLinesThatAreExecutable' => 2,
                'numChangedLinesThatWereExecuted' => 1,
                'changedLinesThatWereNotExecuted' => array(
                    'Example.php' => array(11)
                )
            ),
            $pc->execute(
                __DIR__ . '/fixture/coverage.php',
                __DIR__ . '/fixture/' . $patchFile . '.txt',
                '/tmp/example/'
            )
        );
    }

    public function providerForPatchCoverageIsCalculatedCorrectly()
    {
        return array(
            // Original patch
            array(
                'patch'
            ),
            // Patch showing a renamed file
            array(
                'patch2'
            ),
            // Patch with different start and end numbers
            array(
                'patch3'
            )
        );
    }
}
