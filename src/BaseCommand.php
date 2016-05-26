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
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPUnit_Util_Configuration;

/**
 * @since Class available since Release 2.0.0
 */
abstract class BaseCommand extends AbstractCommand
{
    protected function handleConfiguration(CodeCoverage $coverage, InputInterface $input)
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

    protected function handleFilter(CodeCoverage $coverage, InputInterface $input)
    {
        $filter = $coverage->filter();

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

    protected function handleReports(CodeCoverage $coverage, InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('clover')) {
            $output->write(
                "\nGenerating code coverage report in Clover XML format ..."
            );

            $writer = new CloverReport;
            $writer->process($coverage, $input->getOption('clover'));

            $output->write(" done\n");
        }

        if ($input->getOption('crap4j')) {
            $output->write(
                "\nGenerating code coverage report in Crap4J XML format..."
            );

            $writer = new Crap4jReport;
            $writer->process($coverage, $input->getOption('crap4j'));

            $output->write(" done\n");
        }

        if ($input->getOption('html')) {
            $output->write(
                "\nGenerating code coverage report in HTML format ..."
            );

            $writer = new HtmlReport;
            $writer->process($coverage, $input->getOption('html'));

            $output->write(" done\n");
        }

        if ($input->getOption('php')) {
            $output->write(
                "\nGenerating code coverage report in PHP format ..."
            );

            $writer = new PhpReport;
            $writer->process($coverage, $input->getOption('php'));

            $output->write(" done\n");
        }

        if ($input->getOption('text')) {
            $report = new TextReport;

            $output->write($report->process($coverage));
        }

        if ($input->getOption('xml')) {
            $output->write(
                "\nGenerating code coverage report in PHP format ..."
            );

            $writer = new XmlReport;
            $writer->process($coverage, $input->getOption('xml'));

            $output->write(" done\n");
        }
    }
}
