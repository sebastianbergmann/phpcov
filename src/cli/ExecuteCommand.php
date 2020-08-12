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
use function printf;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;

final class ExecuteCommand extends Command
{
    public function run(Arguments $arguments): int
    {
        if (!is_file($arguments->script())) {
            printf(
                'File "%s" does not exist' . PHP_EOL,
                $arguments->script()
            );

            return 1;
        }

        $filter = new Filter;

        try {
            if ($arguments->pathCoverage()) {
                $driver = Driver::forLineAndPathCoverage($filter);
            } else {
                $driver = Driver::forLineCoverage($filter);
            }

            $coverage = new CodeCoverage($driver, $filter);
        } catch (NoCodeCoverageDriverAvailableException $e) {
            print $e->getMessage() . PHP_EOL;

            return 1;
        }

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
}
