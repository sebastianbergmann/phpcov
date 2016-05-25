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

use SebastianBergmann\CodeCoverage\Util;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since Class available since Release 2.0.0
 */
class PatchCoverageCommand extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('patch-coverage')
             ->addArgument(
                 'coverage',
                 InputArgument::REQUIRED,
                 'Exported code coverage object'
             )
             ->addOption(
                 'patch',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Unified diff to be analysed for patch coverage'
             )
             ->addOption(
                 'path-prefix',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Prefix that needs to be stripped from paths in the diff'
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
        $pc = new PatchCoverage;
        $pc = $pc->execute(
            $input->getArgument('coverage'),
            $input->getOption('patch'),
            $input->getOption('path-prefix')
        );

        $output->writeln(
            sprintf(
                '%d / %d changed executable lines covered (%s)',
                $pc['numChangedLinesThatWereExecuted'],
                $pc['numChangedLinesThatAreExecutable'],
                Util::percent(
                    $pc['numChangedLinesThatWereExecuted'],
                    $pc['numChangedLinesThatAreExecutable'],
                    true
                )
            )
        );

        if (!empty($pc['changedLinesThatWereNotExecuted'])) {
            $output->writeln("\nChanged executable lines that are not covered:\n");

            foreach ($pc['changedLinesThatWereNotExecuted'] as $file => $lines) {
                foreach ($lines as $line) {
                    $output->writeln(
                        sprintf(
                            '  %s:%d',
                            $file,
                            $line
                        )
                    );
                }
            }
        }
    }
}
