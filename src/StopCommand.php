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

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\XdebugSQLite3;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @since Class available since Release 3.0.1
 */
class StopCommand extends BaseCommand
{
    use ExecuteOptionsTrait;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('stop');
        $this->setExecuteOptions($this);
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $driver = XdebugSQLite3::getInstance();
        $coverage = new CodeCoverage($driver);

        $this->handleConfiguration($coverage, $input);
        $this->handleFilter($coverage, $input);

        $coverage->setCurrentId('phpcov');
        $coverage->stop();

        $this->handleReports($coverage, $input, $output);
    }
}