<?php
/*
 * This file is part of phpcov.
 *
 * (c) Scato Eggen <scato.eggen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCOV;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Util;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since Class available since Release <unreleased>
 */
class CheckCoverageCommand extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('check-coverage')
             ->addArgument(
                 'coverage',
                 InputArgument::REQUIRED,
                 'Exported code coverage object'
             )
             ->addArgument(
                 'target',
                 InputArgument::REQUIRED,
                 'Target code coverage percentage'
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
        /** @var CodeCoverage $coverage */
        $coverage = include($input->getArgument('coverage'));

        $numExecutedLines = $coverage->getReport()->getNumExecutedLines();
        $numExecutableLines = $coverage->getReport()->getNumExecutableLines();
        $target = $input->getArgument('target');

        $percentageOfExecutedLines = $numExecutedLines / $numExecutableLines * 100;

        if ($percentageOfExecutedLines >= $target) {
            $output->writeln(
                sprintf(
                    'Code coverage OK (%.1f%% required, %.1f%% reached)',
                    $target,
                    $percentageOfExecutedLines
                )
            );

            return 0;
        } else {
            $output->writeln(
                sprintf(
                    'Insufficient code coverage (%.1f%% required, %.1f%% reached)',
                    $target,
                    $percentageOfExecutedLines
                )
            );

            return 1;
        }
    }
}
