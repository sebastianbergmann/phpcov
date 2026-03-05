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

use function array_keys;
use PHPUnit\TextUI\CliArguments\Builder as CliConfigurationBuilder;
use PHPUnit\TextUI\Configuration\Merger;
use PHPUnit\TextUI\Configuration\SourceMapper;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use SebastianBergmann\CodeCoverage\CodeCoverage;

abstract class Command
{
    abstract public function run(Arguments $arguments): int;

    protected function handleConfiguration(CodeCoverage $coverage, Arguments $arguments): void
    {
        $configuration = $arguments->configuration();

        if ($configuration === null) {
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
}
