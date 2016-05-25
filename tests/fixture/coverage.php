<?php
$coverage = new SebastianBergmann\CodeCoverage\CodeCoverage;

$coverage->setData(
    [
        '/tmp/example/Example.php' => [
           6 =>
                [
                    0 => 'ExampleTest::testOne',
                ],
            7 => null,
            11 => [],
            12 => null,
        ]
    ]
);

$coverage->setTests(
    [
        'ExampleTest::testOne' => 0
    ]
);

$filter = $coverage->filter();
$filter->setWhitelistedFiles([]);

return $coverage;
