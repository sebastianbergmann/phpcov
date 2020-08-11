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
use function printf;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Percentage;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;
use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\Version;

final class Application
{
    public function run(array $argv): int
    {
        $this->printVersion();

        try {
            $arguments = (new ArgumentsBuilder)->build($argv);
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;

            return 1;
        }

        if ($arguments->version()) {
            return 0;
        }

        if ($arguments->help()) {
            $this->help();

            return 0;
        }

        if ($arguments->command() === 'execute') {
            return $this->execute($arguments);
        }

        if ($arguments->command() === 'merge') {
            return $this->merge($arguments);
        }

        if ($arguments->command() === 'patch-coverage') {
            return $this->patchCoverage($arguments);
        }

        $this->help();

        return 1;
    }

    private function execute(Arguments $arguments): int
    {
        if (!is_file($arguments->script())) {
            printf(
                'File "%s" does not exist' . PHP_EOL,
                $arguments->script()
            );

            return 1;
        }

        $filter = new Filter;

        $coverage = new CodeCoverage(
            Driver::forLineCoverage($filter),
            $filter
        );

        $this->handleConfiguration($coverage, $arguments);
        $this->handleFilter($coverage, $arguments);

        if ($filter->isEmpty()) {
            print 'No list of files to be included in code coverage configured' . PHP_EOL;

            return 1;
        }

        if (!$arguments->reportConfigured()) {
            print 'No code coverage report configured' . PHP_EOL;

            return 1;
        }

        $coverage->start('phpcov');

        require $arguments->script();

        /* @noinspection UnusedFunctionResultInspection */
        $coverage->stop();

        $this->handleReports($coverage, $arguments);

        return 0;
    }

    private function merge(Arguments $arguments): int
    {
        $finder = new FinderFacade(
            [$arguments->directory()],
            [],
            ['*.cov']
        );

        $errors = [];

        foreach ($finder->findFiles() as $file) {
            $_coverage = include($file);

            if (!$_coverage instanceof CodeCoverage) {
                $errors[] = $file;

                unset($_coverage);

                continue;
            }

            if (!isset($mergedCoverage)) {
                $mergedCoverage = $_coverage;

                continue;
            }

            $mergedCoverage->merge($_coverage);

            unset($_coverage);
        }

        if (!isset($mergedCoverage)) {
            foreach ($errors as $error) {
                print 'Failed to merge: ' . $error . PHP_EOL;
            }

            return 1;
        }

        $this->handleReports($mergedCoverage, $arguments);

        foreach ($errors as $error) {
            print 'Failed to merge: ' . $error . PHP_EOL;
        }

        return empty($errors) ? 0 : 1;
    }

    private function patchCoverage(Arguments $arguments): int
    {
        $patchCoverage = (new PatchCoverage)->execute(
            $arguments->coverage(),
            $arguments->patch(),
            $arguments->pathPrefix()
        );

        printf(
            '%d / %d changed executable lines covered (%s)' . PHP_EOL,
            $patchCoverage['numChangedLinesThatWereExecuted'],
            $patchCoverage['numChangedLinesThatAreExecutable'],
            Percentage::fromFractionAndTotal(
                $patchCoverage['numChangedLinesThatWereExecuted'],
                $patchCoverage['numChangedLinesThatAreExecutable']
            )->asFixedWidthString()
        );

        if (!empty($patchCoverage['changedLinesThatWereNotExecuted'])) {
            print PHP_EOL . 'Changed executable lines that are not covered:' . PHP_EOL;

            foreach ($patchCoverage['changedLinesThatWereNotExecuted'] as $file => $lines) {
                foreach ($lines as $line) {
                    printf(
                        '  %s:%d' . PHP_EOL,
                        $file,
                        $line
                    );
                }
            }

            return 1;
        }

        return 0;
    }

    private function help(): void
    {
        print <<<'EOT'
Usage:
  phpcov execute       [options] <script>
  phpcov merge         [options] <directory with *.cov files>
  phpcov path-coverage [options] <coverage file> <patch file>

Options for "phpcov execute":

  --configuration <file> Load PHPUnit configuration from XML configuration
  --include <directory>  Include <directory> in code coverage analysis
  --add-uncovered        Include uncovered files in code coverage report
  --process-uncovered    Process uncovered file for code coverage report

Options common for both "phpcov execute" and "phpcov merge":

  --clover <file>        Generate code coverage report in Clover XML format
  --crap4j <file>        Generate code coverage report in Crap4J XML format
  --html <directory>     Generate code coverage report in HTML format
  --php <file>           Export php-code-coverage object
  --text <file>          Generate code coverage report in text format
  --xml <directory>      Generate code coverage report in PHPUnit XML format

Options for "phpcov patch-coverage":

  --path-prefix <prefix> Prefix that needs to be stripped from paths in the patch

EOT;
    }

    private function handleConfiguration(CodeCoverage $coverage, Arguments $arguments): void
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

    private function handleFilter(CodeCoverage $coverage, Arguments $arguments): void
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

    private function handleReports(CodeCoverage $coverage, Arguments $arguments): void
    {
        if ($arguments->clover()) {
            print PHP_EOL . 'Generating code coverage report in Clover XML format ... ';

            $writer = new CloverReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->clover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->crap4j()) {
            print PHP_EOL . 'Generating code coverage report in Crap4J XML format ... ';

            $writer = new Crap4jReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->crap4j());

            print 'done' . PHP_EOL;
        }

        if ($arguments->html()) {
            print PHP_EOL . 'Generating code coverage report in HTML format ... ';

            $writer = new HtmlReport;

            $writer->process($coverage, $arguments->html());

            print 'done' . PHP_EOL;
        }

        if ($arguments->php()) {
            print PHP_EOL . 'Generating code coverage report in PHP format ... ';

            $writer = new PhpReport;

            /* @noinspection UnusedFunctionResultInspection */
            $writer->process($coverage, $arguments->php());

            print 'done' . PHP_EOL;
        }

        if ($arguments->text()) {
            print PHP_EOL . 'Generating code coverage report in text format ... ';

            $writer = new TextReport;

            file_put_contents(
                $arguments->text(),
                $writer->process($coverage, false)
            );

            print 'done' . PHP_EOL;
        }

        if ($arguments->xml()) {
            print PHP_EOL . 'Generating code coverage report in XML format ... ';

            $writer = new XmlReport('unknown');

            $writer->process($coverage, $arguments->xml());

            print 'done' . PHP_EOL;
        }
    }

    private function printVersion(): void
    {
        printf(
            "phpcov %s by Sebastian Bergmann.\n\n",
            $this->version()
        );
    }

    private function version(): string
    {
        return (new Version('8.0', dirname(__DIR__)))->getVersion();
    }
}
