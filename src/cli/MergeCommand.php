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
use function is_dir;
use function printf;
use function realpath;
use function serialize;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Node\Builder;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Cobertura as CoberturaReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\OpenClover as OpenCloverReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;
use SebastianBergmann\CodeCoverage\Serialization\Merger as CoverageMerger;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingSourceAnalyser;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Version;
use SebastianBergmann\FileIterator\Facade;

/**
 * @phpstan-import-type TestType from CodeCoverage
 */
final class MergeCommand implements Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_dir($arguments->directory())) {
            printf(
                '"%s" is not a directory' . PHP_EOL,
                $arguments->directory(),
            );

            return 1;
        }

        if (!$arguments->reportConfigured()) {
            print 'No code coverage report configured' . PHP_EOL;

            return 1;
        }

        $files = (new Facade)->getFilesAsArray(
            $arguments->directory(),
            ['.cov'],
        );

        if ($files === []) {
            printf(
                'No "%s/*.cov" files found' . PHP_EOL,
                realpath($arguments->directory()),
            );

            return 1;
        }

        try {
            $merged = (new CoverageMerger)->merge($files);
        } catch (CodeCoverageException $e) {
            print $e->getMessage() . PHP_EOL;

            return 1;
        }

        if ($arguments->php() !== null) {
            print 'Generating code coverage report in PHP format ... ';

            Filesystem::write(
                $arguments->php(),
                '<?php // phpunit/php-code-coverage version ' . Version::id() . PHP_EOL .
                "return \unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'" . PHP_EOL .
                serialize($merged) . PHP_EOL .
                'END_OF_COVERAGE_SERIALIZATION' . PHP_EOL .
                ');',
            );

            print 'done' . PHP_EOL;
        }

        $this->handleReports($merged['codeCoverage'], $merged['testResults'], $arguments);

        return 0;
    }

    /**
     * @param array<string, TestType> $testResults
     */
    private function handleReports(ProcessedCodeCoverageData $codeCoverage, array $testResults, Arguments $arguments): void
    {
        $report = $this->buildReport($codeCoverage, $testResults);

        if ($arguments->clover() !== null) {
            print 'Generating code coverage report in Clover XML format ... ';

            $writer = new CloverReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($report, $arguments->clover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->openClover() !== null) {
            print 'Generating code coverage report in OpenClover XML format ... ';

            $writer = new OpenCloverReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($report, $arguments->clover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->cobertura() !== null) {
            print 'Generating code coverage report in Cobertura XML format ... ';

            $writer = new CoberturaReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($report, $arguments->cobertura());

            print 'done' . PHP_EOL;
        }

        if ($arguments->crap4j() !== null) {
            print 'Generating code coverage report in Crap4J XML format ... ';

            $writer = new Crap4jReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($report, $arguments->crap4j());

            print 'done' . PHP_EOL;
        }

        if ($arguments->html() !== null) {
            print 'Generating code coverage report in HTML format ... ';

            $writer = new HtmlReport;

            $writer->process($report, $arguments->html());

            print 'done' . PHP_EOL;
        }

        if ($arguments->text() !== null) {
            print 'Generating code coverage report in text format ... ';

            $writer = new TextReport(Thresholds::default());

            file_put_contents(
                $arguments->text(),
                $writer->process($report),
            );

            print 'done' . PHP_EOL;
        }

        if ($arguments->xml() !== null) {
            print 'Generating code coverage report in XML format ... ';

            $writer = new XmlReport;

            $writer->process($arguments->xml(), $report, $testResults);

            print 'done' . PHP_EOL;
        }
    }

    /**
     * @param array<string, TestType> $testResults
     */
    private function buildReport(ProcessedCodeCoverageData $codeCoverage, array $testResults): Directory
    {
        return (new Builder(new FileAnalyser(new ParsingSourceAnalyser, false, false)))->build($codeCoverage, $testResults);
    }
}
