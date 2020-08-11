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

final class ArgumentsBuilder
{
    private const COMMANDS = [
        'execute' => [
            'longOptions' => [
                'configuration=',
                'include=',
                'add-uncovered',
                'process-uncovered',
                'clover=',
                'crap4j=',
                'html=',
                'php=',
                'text=',
                'xml=',
                'help',
            ],
            'shortOptions' => '',
            'arguments'    => [
                'script',
            ],
        ],

        'merge' => [
            'longOptions' => [
                'clover=',
                'crap4j=',
                'html=',
                'php=',
                'text=',
                'xml=',
                'help',
            ],
            'shortOptions' => '',
            'arguments'    => [
                'directory',
            ],
        ],

        'patch-coverage' => [
            'longOptions' => [
                'path-prefix=',
                'help',
            ],
            'shortOptions' => '',
            'arguments'    => [
                'coverage',
                'patch',
            ],
        ],
    ];

    public function build(array $argv): Arguments
    {
        if (!isset($argv[1])) {
            throw new CommandMissingException;
        }

        if (!isset(self::COMMANDS[$argv[1]])) {
            throw new UnknownCommandException($argv[1]);
        }

        $command = $argv[1];

        $options = Getopt::parse(
            $argv,
            self::COMMANDS[$command]['shortOptions'],
            self::COMMANDS[$command]['longOptions']
        );

        $script    = null;
        $directory = null;
        $coverage  = null;
        $patch     = null;

        foreach (self::COMMANDS[$command]['arguments'] as $position => $argument) {
            if (!isset($options[1][$position + 1])) {
                throw new RequiredArgumentMissingException($argument);
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

        $configuration    = null;
        $include          = [];
        $addUncovered     = false;
        $processUncovered = false;
        $clover           = null;
        $crap4j           = null;
        $html             = null;
        $php              = null;
        $text             = null;
        $xml              = null;
        $help             = false;
        $pathPrefix       = null;

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--configuration':
                    $configuration = $option[1];

                    break;

                case '--include':
                    $include[] = $option[1];

                    break;

                case '--add-uncovered':
                    $addUncovered = true;

                    break;

                case '--process-uncovered':
                    $processUncovered = true;

                    break;

                case '--help':
                    $help = true;

                    break;

                case '--clover':
                    $clover = $option[1];

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
            $addUncovered,
            $processUncovered,
            $clover,
            $crap4j,
            $html,
            $php,
            $text,
            $xml,
            $help,
            $pathPrefix
        );
    }
}
