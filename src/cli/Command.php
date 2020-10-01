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
use function file_put_contents;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Cobertura as CoberturaReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
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

    protected function handleFilter(CodeCoverage $coverage, Arguments $arguments): void
    {
        if ($arguments->addUncovered()) {
            $coverage->includeUncoveredFiles();
        } else {
            $coverage->excludeUncoveredFiles();
        }

        if ($arguments->processUncovered()) {
            $coverage->processUncoveredFiles();
        } else {
            $coverage->doNotProcessUncoveredFiles();
        }

        foreach ($arguments->include() as $item) {
            if (is_dir($item)) {
                $coverage->filter()->includeDirectory($item);
            } elseif (is_file($item)) {
                $coverage->filter()->includeFile($item);
            }
        }
    }

    protected function handleReports(CodeCoverage $coverage, Arguments $arguments): void
    {
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

        if ($arguments->php()) {
            print 'Generating code coverage report in PHP format ... ';

            $writer = new PhpReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->php());

            print 'done' . PHP_EOL;
        }

        if ($arguments->text()) {
            print 'Generating code coverage report in text format ... ';

            $writer = new TextReport;

            file_put_contents(
                $arguments->text(),
                $writer->process($coverage, false)
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
