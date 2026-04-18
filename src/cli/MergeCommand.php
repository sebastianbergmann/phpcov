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
use function is_dir;
use function printf;
use function realpath;
use function serialize;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Report\Facade as ReportFacade;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Serialization\Merger as CoverageMerger;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\FileIterator\Facade;

final class MergeCommand implements Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_dir($arguments->directory())) {
            printf(
                '"%s" is not a directory' . PHP_EOL,
                $arguments->directory(),
            );

            return 255;
        }

        if (!$arguments->reportConfigured()) {
            print 'No code coverage report configured' . PHP_EOL;

            return 255;
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

            return 255;
        }

        try {
            $merged = (new CoverageMerger)->merge(
                $files,
                $arguments->requireMatchingGitInformation(),
                $arguments->requireMatchingPhpVersion(),
                $arguments->requireMatchingCodeCoverageDriver(),
            );
            // @codeCoverageIgnoreStart
        } catch (CodeCoverageException $e) {
            print $e->getMessage() . PHP_EOL;

            return 255;
            // @codeCoverageIgnoreEnd
        }

        if ($arguments->php() !== null) {
            print 'Generating code coverage report in PHP format ... ';

            Filesystem::write(
                $arguments->php(),
                '<?php // phpunit/php-code-coverage serialization format ' . Serializer::SERIALIZATION_FORMAT . PHP_EOL .
                "return \unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'" . PHP_EOL .
                serialize($merged) . PHP_EOL .
                'END_OF_COVERAGE_SERIALIZATION' . PHP_EOL .
                ');',
            );

            print 'done' . PHP_EOL;
        }

        if ($arguments->source() !== null) {
            $merged['basePath'] = $arguments->source();
        }

        $reportFacade = ReportFacade::fromSerializedData($merged);

        if ($arguments->clover() !== null) {
            print 'Generating code coverage report in Clover XML format ... ';

            $reportFacade->renderClover($arguments->clover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->openClover() !== null) {
            print 'Generating code coverage report in OpenClover XML format ... ';

            $reportFacade->renderOpenClover($arguments->openClover());

            print 'done' . PHP_EOL;
        }

        if ($arguments->cobertura() !== null) {
            print 'Generating code coverage report in Cobertura XML format ... ';

            $reportFacade->renderCobertura($arguments->cobertura());

            print 'done' . PHP_EOL;
        }

        if ($arguments->crap4j() !== null) {
            print 'Generating code coverage report in Crap4J XML format ... ';

            $reportFacade->renderCrap4j($arguments->crap4j());

            print 'done' . PHP_EOL;
        }

        if ($arguments->html() !== null) {
            print 'Generating code coverage report in HTML format ... ';

            $reportFacade->renderHtml($arguments->html());

            print 'done' . PHP_EOL;
        }

        if ($arguments->text() !== null) {
            print 'Generating code coverage report in text format ... ';

            $reportFacade->renderText($arguments->text(), Thresholds::default());

            print 'done' . PHP_EOL;
        }

        if ($arguments->xml() !== null) {
            print 'Generating code coverage report in XML format ... ';

            $reportFacade->renderXml($arguments->xml());

            print 'done' . PHP_EOL;
        }

        return 0;
    }
}
