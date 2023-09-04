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

use function array_merge;
use SebastianBergmann\CliParser\Exception as CliParserException;
use SebastianBergmann\CliParser\Parser as CliParser;

final class ArgumentsBuilder
{
    private const COMMANDS = [
        'execute' => [
            'longOptions' => [
                'configuration=',
                'include=',
                'add-uncovered',
                'path-coverage',
                'clover=',
                'cobertura=',
                'crap4j=',
                'html=',
                'php=',
                'text=',
                'xml=',
            ],
            'arguments' => [
                'script',
            ],
        ],

        'merge' => [
            'longOptions' => [
                'clover=',
                'cobertura=',
                'crap4j=',
                'html=',
                'php=',
                'text=',
                'xml=',
            ],
            'arguments' => [
                'directory',
            ],
        ],

        'patch-coverage' => [
            'longOptions' => [
                'path-prefix=',
            ],
            'arguments' => [
                'coverage',
                'patch',
            ],
        ],
    ];

    /**
     * @throws ArgumentsBuilderException
     */
    public function build(array $argv): Arguments
    {
        $longOptions = [
            'help',
            'version',
        ];

        $command = null;

        if (isset($argv[1], self::COMMANDS[$argv[1]])) {
            $command     = $argv[1];
            $longOptions = array_merge($longOptions, self::COMMANDS[$command]['longOptions']);
        }

        try {
            $options = (new CliParser)->parse(
                $argv,
                'hv',
                $longOptions,
            );
        } catch (CliParserException $e) {
            throw new ArgumentsBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $script    = null;
        $directory = null;
        $coverage  = null;
        $patch     = null;

        if ($command) {
            foreach (self::COMMANDS[$command]['arguments'] as $position => $argument) {
                if (!isset($options[1][$position + 1])) {
                    throw new RequiredArgumentMissingException($argument);
                }
            }
        }

        switch ($command) {
            case 'execute':
                $script = $options[1][1];

                break;

            case 'merge':
                $directory = $options[1][1];

                break;

            case 'patch-coverage':
                $coverage = $options[1][1];
                $patch    = $options[1][2];

                break;
        }

        $configuration = null;
        $include       = [];
        $pathCoverage  = false;
        $addUncovered  = false;
        $clover        = null;
        $cobertura     = null;
        $crap4j        = null;
        $html          = null;
        $php           = null;
        $text          = null;
        $xml           = null;
        $pathPrefix    = null;
        $help          = false;
        $version       = false;

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--configuration':
                    $configuration = $option[1];

                    break;

                case '--include':
                    $include[] = $option[1];

                    break;

                case '--path-coverage':
                    $pathCoverage = true;

                    break;

                case '--add-uncovered':
                    $addUncovered = true;

                    break;

                case '--clover':
                    $clover = $option[1];

                    break;

                case '--cobertura':
                    $cobertura = $option[1];

                    break;

                case '--crap4j':
                    $crap4j = $option[1];

                    break;

                case '--html':
                    $html = $option[1];

                    break;

                case '--php':
                    $php = $option[1];

                    break;

                case '--text':
                    $text = $option[1];

                    break;

                case '--xml':
                    $xml = $option[1];

                    break;

                case '--path-prefix':
                    $pathPrefix = $option[1];

                    break;

                case 'h':
                case '--help':
                    $help = true;

                    break;

                case 'v':
                case '--version':
                    $version = true;

                    break;
            }
        }

        return new Arguments(
            $command,
            $script,
            $directory,
            $coverage,
            $patch,
            $configuration,
            $include,
            $pathCoverage,
            $addUncovered,
            $clover,
            $cobertura,
            $crap4j,
            $html,
            $php,
            $text,
            $xml,
            $pathPrefix,
            $help,
            $version,
        );
    }
}
