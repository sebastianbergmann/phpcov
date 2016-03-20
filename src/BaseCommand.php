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

use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHP_CodeCoverage;
use PHP_CodeCoverage_Report_Clover;
use PHP_CodeCoverage_Report_Crap4j;
use PHP_CodeCoverage_Report_HTML;
use PHP_CodeCoverage_Report_PHP;
use PHP_CodeCoverage_Report_Text;
use PHPUnit_Util_Configuration;
use ReflectionClass;

/**
 * @since     Class available since Release 2.0.0
 */
abstract class BaseCommand extends AbstractCommand
{
    protected function handleConfiguration(PHP_CodeCoverage $coverage, InputInterface $input)
    {
        $configuration = $input->getOption('configuration');

        if (!$configuration) {
            return;
        }

        $filter        = $coverage->filter();
        $configuration = PHPUnit_Util_Configuration::getInstance($configuration);

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

    protected function handleFilter(PHP_CodeCoverage $coverage, InputInterface $input)
    {
        $filter = $coverage->filter();

        $whitelist = $input->getOption('whitelist');
        if (empty($whitelist)) {
            $classes = [
                'SebastianBergmann\PHPCOV\Application',
                'SebastianBergmann\FinderFacade\FinderFacade',
                'SebastianBergmann\Version',
                'Symfony\Component\Console\Application',
                'Symfony\Component\Finder\Finder'
            ];

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

    protected function handleReports(PHP_CodeCoverage $coverage, InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('clover')) {
            $output->write(
                "\nGenerating code coverage report in Clover XML format ..."
            );

            $writer = new PHP_CodeCoverage_Report_Clover;
            $writer->process($coverage, $input->getOption('clover'));

            $output->write(" done\n");
        }

        if ($input->getOption('crap4j')) {
            $output->write(
                "\nGenerating code coverage report in Crap4J XML format..."
            );

            $writer = new PHP_CodeCoverage_Report_Crap4j;
            $writer->process($coverage, $input->getOption('crap4j'));

            $output->write(" done\n");
        }

        if ($input->getOption('html')) {
            $output->write(
                "\nGenerating code coverage report in HTML format ..."
            );

            $writer = new PHP_CodeCoverage_Report_HTML(
                $input->getOption('low-upper-bound'),
                $input->getOption('high-lower-bound')
            );
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
            $writer = new PHP_CodeCoverage_Report_Text(
                $input->getOption('low-upper-bound'),
                $input->getOption('high-lower-bound'),
                $input->getOption('show-uncovered-files'),
                $input->getOption('show-only-summary')
            );
            $writer->process($coverage, $input->getOption('text'));
        }
    }
}
