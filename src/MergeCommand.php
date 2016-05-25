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
use SebastianBergmann\FinderFacade\FinderFacade;

/**
 * @since Class available since Release 2.0.0
 */
class MergeCommand extends BaseCommand
{
    /**
     * @var string[]
     */
    protected $mergeErrors;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('merge')
             ->addArgument(
                 'directory',
                 InputArgument::REQUIRED,
                 'Directory to scan for exported code coverage objects stored in .cov files'
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
                InputOption::VALUE_REQUIRED,
                'Generate code coverage report in text format'
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
        $mergedCoverage = new CodeCoverage;

        $finder = new FinderFacade(
            [$input->getArgument('directory')],
            [],
            ['*.cov']
        );

        foreach ($finder->findFiles() as $file) {
            $_coverage = include($file);

            if (! ($_coverage instanceof CodeCoverage)) {
                $this->mergeErrors[] = $file;
                unset($_coverage);
                continue;
            }

            $mergedCoverage->merge($_coverage);
            unset($_coverage);
        }

        $this->handleReports($mergedCoverage, $input, $output);
        $this->outputMergeErrors($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function outputMergeErrors(OutputInterface $output)
    {
        if (empty($this->mergeErrors)) {
            return;
        }

        foreach ($this->mergeErrors as $mergeError) {
            $output->write('Failed to merge: ' . $mergeError, true);
        }
    }
}
