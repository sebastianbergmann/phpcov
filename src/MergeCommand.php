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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SebastianBergmann\FinderFacade\FinderFacade;
use PHP_CodeCoverage;

/**
 * @since     Class available since Release 2.0.0
 */
class MergeCommand extends BaseCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('merge')
             ->addArgument(
                 'directory',
                 InputArgument::REQUIRED,
                 'Directory to scan for exported PHP_CodeCoverage objects stored in .cov files'
             )
             ->addOption(
                 'clover',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in Clover XML format'
             )
             ->addOption(
                 'crap4j',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in Crap4J XML format'
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
                 'Export PHP_CodeCoverage object to file'
             )
             ->addOption(
                 'text',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Generate code coverage report in text format'
             );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new FinderFacade(
            array($input->getArgument('directory')),
            array(),
            array('*.cov')
        );

        $files = $finder->findFiles();

        if (count($files) > 0) {
            $file = array_shift($files);

            $mergedCoverage = include($file);

            foreach ($files as $file) {
                $_coverage = include($file);
                $mergedCoverage->merge($_coverage);
                unset($_coverage);
            }

            $this->handleReports($mergedCoverage, $input, $output);
        }
    }
}
