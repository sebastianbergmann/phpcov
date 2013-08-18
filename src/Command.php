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

use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SebastianBergmann\FinderFacade\FinderFacade;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Report_Clover;
use PHP_CodeCoverage_Report_HTML;
use PHP_CodeCoverage_Report_PHP;
use PHP_CodeCoverage_Report_Text;
use PHPUnit_Util_Configuration;
use ReflectionClass;

/**
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2011-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/php-code-coverage/tree
 * @since     Class available since Release 2.0.0
 */
class Command extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('phpcov')
             ->addArgument(
                 'argument',
                 InputArgument::OPTIONAL
             )
             ->addOption(
                 'configuration',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Read configuration from XML file'
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
                 'php',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Serialize PHP_CodeCoverage object to file'
             )
             ->addOption(
                 'text',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in text format'
             )
             ->addOption(
                 'blacklist',
                 null,
                 InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                 'Add directory or file to the blacklist'
             )
             ->addOption(
                 'whitelist',
                 null,
                 InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                 'Add directory or file to the whitelist'
             )
             ->addOption(
                 'merge',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Merge serialized PHP_CodeCoverage objects stored in .cov files'
             )
             ->addOption(
                 'add-uncovered',
                 null,
                 InputOption::VALUE_NONE,
                 'Add whitelisted files that are not covered'
             )
             ->addOption(
                 'process-uncovered',
                 null,
                 InputOption::VALUE_NONE,
                 'Process whitelisted files that are not covered'
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
        $coverage      = new PHP_CodeCoverage;
        $configuration = $input->getOption('configuration');

        if ($configuration) {
            $this->handleConfiguration($coverage, $configuration);
        }

        $this->handleFilter($coverage, $input);

        if ($input->getArgument('argument') === null &&
            $input->getOption('merge')) {
            $this->executeMerge($coverage, $input->getOption('merge'));
        } elseif ($input->getArgument('argument') !== null &&
                   !$input->getOption('merge')) {
            $this->executeScript($coverage, $input->getArgument('argument'));
        }

        if ($input->getOption('clover')) {
            $output->write(
                "\nGenerating code coverage report in Clover XML format ..."
            );

            $writer = new PHP_CodeCoverage_Report_Clover;
            $writer->process($coverage, $input->getOption('clover'));

            $output->write(" done\n");
        }

        if ($input->getOption('html')) {
            $output->write(
                "\nGenerating code coverage report in HTML format ..."
            );

            $writer = new PHP_CodeCoverage_Report_HTML;
            $writer->process($coverage, $input->getOption('html'));

            $output->write(" done\n");
        }

        if ($input->getOption('php')) {
            $output->write(
                "\nGenerating code coverage report in PHP format ..."
            );

            $writer = new PHP_CodeCoverage_Report_PHP;
            $writer->process($coverage, $input->getOption('php'));

            $output->write(" done\n");
        }

        if ($input->getOption('text')) {
            $writer = new PHP_CodeCoverage_Report_Text;
            $writer->process($coverage, $input->getOption('text'));
        }
    }

    /**
     * @param PHP_CodeCoverage $coverage
     * @param string           $directory
     */
    private function executeMerge(PHP_CodeCoverage $coverage, $directory)
    {
        $finder = new FinderFacade(array($directory), array(), array('*.cov'));

        foreach ($finder->findFiles() as $file) {
            $coverage->merge(unserialize(file_get_contents($file)));
        }
    }

    /**
     * @param PHP_CodeCoverage $coverage
     * @param string           $script
     */
    private function executeScript(PHP_CodeCoverage $coverage, $script)
    {
        $coverage->start('phpcov');

        require $script;

        $coverage->stop();
    }

    /**
     * @param PHP_CodeCoverage $coverage
     * @param string           $filename
     */
    private function handleConfiguration(PHP_CodeCoverage $coverage, $filename)
    {
        $filter        = $coverage->filter();
        $configuration = PHPUnit_Util_Configuration::getInstance($filename);

        $filterConfiguration = $configuration->getFilterConfiguration();

        $coverage->setAddUncoveredFilesFromWhitelist(
            $filterConfiguration['whitelist']['addUncoveredFilesFromWhitelist']
        );

        $coverage->setProcessUncoveredFilesFromWhitelist(
            $filterConfiguration['whitelist']['processUncoveredFilesFromWhitelist']
        );

        foreach ($filterConfiguration['blacklist']['include']['directory'] as $dir) {
            $filter->addDirectoryToBlacklist(
                $dir['path'],
                $dir['suffix'],
                $dir['prefix'],
                $dir['group']
            );
        }

        foreach ($filterConfiguration['blacklist']['include']['file'] as $file) {
            $filter->addFileToBlacklist($file);
        }

        foreach ($filterConfiguration['blacklist']['exclude']['directory'] as $dir) {
            $filter->removeDirectoryFromBlacklist(
                $dir['path'],
                $dir['suffix'],
                $dir['prefix'],
                $dir['group']
            );
        }

        foreach ($filterConfiguration['blacklist']['exclude']['file'] as $file) {
            $filter->removeFileFromBlacklist($file);
        }

        foreach ($filterConfiguration['whitelist']['include']['directory'] as $dir) {
            $filter->addDirectoryToWhitelist(
                $dir['path'],
                $dir['suffix'],
                $dir['prefix']
            );
        }

        foreach ($filterConfiguration['whitelist']['include']['file'] as $file) {
            $filter->addFileToWhitelist($file);
        }

        foreach ($filterConfiguration['whitelist']['exclude']['directory'] as $dir) {
            $filter->removeDirectoryFromWhitelist(
                $dir['path'],
                $dir['suffix'],
                $dir['prefix']
            );
        }

        foreach ($filterConfiguration['whitelist']['exclude']['file'] as $file) {
            $filter->removeFileFromWhitelist($file);
        }
    }

    private function handleFilter(PHP_CodeCoverage $coverage, InputInterface $input)
    {
        $filter = $coverage->filter();

        if (empty($input->getOption('whitelist'))) {
            $classes = array(
                'SebastianBergmann\PHPCOV\Application',
                'SebastianBergmann\FinderFacade\FinderFacade',
                'SebastianBergmann\Version',
                'Symfony\Component\Console\Application',
                'Symfony\Component\Finder\Finder'
            );

            foreach ($classes as $class) {
                $c = new ReflectionClass($class);
                $filter->addDirectoryToBlacklist(dirname($c->getFileName()));
            }

            foreach ($input->getOption('blacklist') as $item) {
                if (is_dir($item)) {
                    $filter->addDirectoryToBlacklist($item);
                } elseif (is_file($item)) {
                    $filter->addFileToBlacklist($item);
                }
            }
        } else {
            $coverage->setAddUncoveredFilesFromWhitelist(
                $input->getOption('add-uncovered')
            );

            $coverage->setProcessUncoveredFilesFromWhitelist(
                $input->getOption('process-uncovered')
            );

            foreach ($input->getOption('whitelist') as $item) {
                if (is_dir($item)) {
                    $filter->addDirectoryToWhitelist($item);
                } elseif (is_file($item)) {
                    $filter->addFileToWhitelist($item);
                }
            }
        }
    }
}
