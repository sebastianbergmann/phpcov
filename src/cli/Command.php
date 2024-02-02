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

use const PHP_EOL;
use function array_keys;
use function file_put_contents;
use PHPUnit\TextUI\CliArguments\Builder as CliConfigurationBuilder;
use PHPUnit\TextUI\Configuration\Merger;
use PHPUnit\TextUI\Configuration\SourceMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Cobertura as CoberturaReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;

abstract class Command
{
    abstract public function run(Arguments $arguments): int;

    protected function handleConfiguration(CodeCoverage $coverage, Arguments $arguments): void
    {
        $configuration = $arguments->configuration();

        if (!$configuration) {
            return;
        }

        $cliConfiguration = (new CliConfigurationBuilder)->fromParameters([]);
        $xmlConfiguration = (new Loader)->load($configuration);
        $configuration    = (new Merger)->merge($cliConfiguration, $xmlConfiguration);

        if ($configuration->includeUncoveredFiles()) {
            $coverage->includeUncoveredFiles();
        } else {
            $coverage->excludeUncoveredFiles();
        }

        if ($configuration->source()->notEmpty()) {
            $coverage->filter()->includeFiles(
                array_keys(
                    (new SourceMapper)->map(
                        $configuration->source(),
                    ),
                ),
            );
        }
    }

    protected function handleReports(CodeCoverage $coverage, Arguments $arguments): void
    {
        if ($arguments->php()) {
            print 'Generating code coverage report in PHP format ... ';

            $writer = new PhpReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->php());

            print 'done' . PHP_EOL;
        }

        if ($arguments->clover()) {
            print 'Generating code coverage report in Clover XML format ... ';

            $writer = new CloverReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->clover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->cobertura()) {
            print 'Generating code coverage report in Cobertura XML format ... ';

            $writer = new CoberturaReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->cobertura());

            print 'done' . PHP_EOL;
        }

        if ($arguments->crap4j()) {
            print 'Generating code coverage report in Crap4J XML format ... ';

            $writer = new Crap4jReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->crap4j());

            print 'done' . PHP_EOL;
        }

        if ($arguments->html()) {
            print 'Generating code coverage report in HTML format ... ';

            $writer = new HtmlReport;

            $writer->process($coverage, $arguments->html());

            print 'done' . PHP_EOL;
        }

        if ($arguments->text()) {
            print 'Generating code coverage report in text format ... ';

            $writer = new TextReport(Thresholds::default());

            file_put_contents(
                $arguments->text(),
                $writer->process($coverage),
            );

            print 'done' . PHP_EOL;
        }

        if ($arguments->xml()) {
            print 'Generating code coverage report in XML format ... ';

            $writer = new XmlReport('unknown');

            $writer->process($coverage, $arguments->xml());

            print 'done' . PHP_EOL;
        }
    }
}
