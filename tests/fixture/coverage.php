<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$coverage = new SebastianBergmann\CodeCoverage\CodeCoverage;

$coverage->setData(
    [
        '/tmp/example/Example.php' => [
            6 => [
                0 => 'ExampleTest::testOne',
            ],
            7  => null,
            11 => [],
            12 => null,
        ],
    ]
);

$coverage->setTests(
    [
        'ExampleTest::testOne' => 0,
    ]
);

$filter = $coverage->filter();
$filter->setWhitelistedFiles([]);

return $coverage;
