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

use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since Class available since Release 2.0.0
 */
class ExecuteCommand extends BaseCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('execute')
             ->addArgument(
                 'script',
                 InputArgument::REQUIRED,
                 'Script to execute'
             )
             ->addOption(
                 'configuration',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Read configuration from XML file'
             )
             ->addOption(
                 'whitelist',
                 null,
                 InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                 'Add directory or file to the whitelist'
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
                'Export code coverage object to file'
            )
            ->addOption(
                'text',
                null,
                InputOption::VALUE_NONE,
                'Write code coverage report in text format to STDOUT'
            )
            ->addOption(
                'xml',
                null,
                InputOption::VALUE_REQUIRED,
                'Generate code coverage report in PHPUnit XML format'
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
        $coverage = new CodeCoverage;

        $this->handleConfiguration($coverage, $input);
        $this->handleFilter($coverage, $input);

        $coverage->start('phpcov');

        require $input->getArgument('script');

        $coverage->stop();

        $this->handleReports($coverage, $input, $output);
    }
}
