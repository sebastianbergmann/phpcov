<?php

/**
 * Include this in any file to start coverage, coverage will automatically end when process dies.
 */
require_once dirname(__FILE__).'/../../../../autoload.php';

use SebastianBergmann\CodeCoverage\Driver\XdebugSQLite3 as Driver;
use SebastianBergmann\CodeCoverage\CodeCoverage;

if (Driver::isCoverageOn()) {
    $driver = Driver::getInstance();
    $coverage = new CodeCoverage($driver);
    $coverage->start('phpcov');
    register_shutdown_function('stop_coverage');
}

/**
 * Stops the current coverage analysis.
 */
function stop_coverage()
{
    // hack until i can think of a way to run tests first and w/o exiting.
    $autorun = function_exists('run_local_tests');
    if ($autorun) {
        $result = run_local_tests();
    }
    Driver::getInstance()->stop();
    if ($autorun) {
        exit($result ? 0 : 1);
    }
}
