<?php
/**
 * phpcov
 *
 * Copyright (c) 2011-2013, Sebastian Bergmann <sebastian@phpunit.de>.
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
 * @copyright 2011-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 2.0.0
 */

namespace SebastianBergmann\PHPCOV;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHP_CodeCoverage;

/**
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @author    Hans-Peter Buniat (hpbuniat@googlemail.com)
 * @copyright 2011-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/php-code-coverage/tree
 * @since     Class available since Release 2.0.0
 */
class TransformCommand extends BaseCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('transform')
             ->addArgument(
                 'coverage-file',
                 InputArgument::REQUIRED,
                 'Read existing PHP_CodeCoverage object from file'
             )
             ->addOption(
                 'clover',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in Clover XML format'
             )
             ->addOption(
                 'html',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in HTML format'
             )
             ->addOption(
                 'crap4j',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in Crap4J XML format'
             )
             ->addOption(
                 'text',
                 null,
                 InputOption::VALUE_NONE,
                 'Generate code coverage report in text format'
             )
             ->addOption(
                 'php',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Export PHP_CodeCoverage object to file'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $coverage = new PHP_CodeCoverage;

        $_coverage = include($input->getArgument('coverage-file'));
        $coverage->merge($_coverage);
        unset($_coverage);

        $this->handleReports($coverage, $input, $output);
    }
}
