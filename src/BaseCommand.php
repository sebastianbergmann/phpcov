<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCOV;

use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
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

abstract class BaseCommand extends AbstractCommand
{
    protected function handleConfiguration(CodeCoverage $coverage, InputInterface $input): void
    {
        $configuration = $input->getOption('configuration');

        if (!$configuration) {
            return;
        }

        $configuration = (new Loader)->load($configuration);

        (new FilterMapper)->map(
            $coverage->filter(),
            $configuration->codeCoverage()
        );

        if ($configuration->codeCoverage()->includeUncoveredFiles()) {
            $coverage->includeUncoveredFiles();
        } else {
            $coverage->excludeUncoveredFiles();
        }

        if ($configuration->codeCoverage()->processUncoveredFiles()) {
            $coverage->processUncoveredFiles();
        } else {
            $coverage->doNotProcessUncoveredFiles();
        }
    }

    protected function handleFilter(CodeCoverage $coverage, InputInterface $input): void
    {
        if ($input->getOption('add-uncovered')) {
            $coverage->includeUncoveredFiles();
        } else {
            $coverage->excludeUncoveredFiles();
        }

        if ($input->getOption('process-uncovered')) {
            $coverage->processUncoveredFiles();
        } else {
            $coverage->doNotProcessUncoveredFiles();
        }

        foreach ($input->getOption('include') as $item) {
            if (\is_dir($item)) {
                $coverage->filter()->includeDirectory($item);
            } elseif (\is_file($item)) {
                $coverage->filter()->includeFile($item);
            }
        }
    }

    protected function handleReports(CodeCoverage $coverage, InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('clover')) {
            $output->write("\nGenerating code coverage report in Clover XML format ...");

            $writer = new CloverReport;
            $writer->process($coverage, $input->getOption('clover'));

            $output->write(" done\n");
        }

        if ($input->getOption('crap4j')) {
            $output->write("\nGenerating code coverage report in Crap4J XML format...");

            $writer = new Crap4jReport;
            $writer->process($coverage, $input->getOption('crap4j'));

            $output->write(" done\n");
        }

        if ($input->getOption('html')) {
            $output->write("\nGenerating code coverage report in HTML format ...");

            $writer = new HtmlReport;
            $writer->process($coverage, $input->getOption('html'));

            $output->write(" done\n");
        }

        if ($input->getOption('php')) {
            $output->write("\nGenerating code coverage report in PHP format ...");

            $writer = new PhpReport;
            $writer->process($coverage, $input->getOption('php'));

            $output->write(" done\n");
        }

        if ($input->getOption('text')) {
            $report = new TextReport;

            $color = false;

            if ($input->getOption('ansi')) {
                $color = true;
            }

            $output->write($report->process($coverage, $color));
        }

        if ($input->getOption('xml')) {
            $output->write(
                "\nGenerating code coverage report in PHP format ..."
            );

            $writer = new XmlReport('unknown');
            $writer->process($coverage, $input->getOption('xml'));

            $output->write(" done\n");
        }
    }
}
