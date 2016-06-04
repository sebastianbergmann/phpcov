<?php
/**
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Levi Govaerts <legovaer@me.com>
 */

namespace SebastianBergmann\PHPCOV;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @since Class available since Release 3.0.1
 */
trait ExecuteOptionsTrait
{

    /**
     * Adds all execution options and arguments to the command.
     *
     * @param BaseCommand $object
     *   The object that requires the options.
     */
    public function setExecuteOptions(BaseCommand $object)
    {
        $object->addArgument(
            'script',
            InputArgument::OPTIONAL,
            'Script to execute'
        )
        ->addOption(
            'configuration',
            null,
            InputOption::VALUE_REQUIRED,
            'Read configuration from XML file'
        )
        ->addOption(
            'whitelist',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Add directory or file to the whitelist'
        )
        ->addOption(
            'add-uncovered',
            null,
            InputOption::VALUE_NONE,
            'Add whitelisted files that are not covered'
        )
        ->addOption(
            'process-uncovered',
            null,
            InputOption::VALUE_NONE,
            'Process whitelisted files that are not covered'
        )
        ->addOption(
            'clover',
            null,
            InputOption::VALUE_REQUIRED,
            'Generate code coverage report in Clover XML format'
        )
        ->addOption(
            'crap4j',
            null,
            InputOption::VALUE_REQUIRED,
            'Generate code coverage report in Crap4J XML format'
        )
        ->addOption(
            'html',
            null,
            InputOption::VALUE_REQUIRED,
            'Generate code coverage report in HTML format'
        )
        ->addOption(
            'php',
            null,
            InputOption::VALUE_REQUIRED,
            'Export code coverage object to file'
        )
        ->addOption(
            'text',
            null,
            InputOption::VALUE_NONE,
            'Write code coverage report in text format to STDOUT'
        )
        ->addOption(
            'xml',
            null,
            InputOption::VALUE_REQUIRED,
            'Generate code coverage report in PHPUnit XML format'
        );
    }
}
