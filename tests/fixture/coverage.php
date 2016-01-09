<?php
$coverage = new PHP_CodeCoverage;
$coverage->setData(array (
  '/tmp/example/Example.php' =>
  array (
    6 =>
    array (
      0 => 'ExampleTest::testOne',
    ),
    7 => NULL,
    11 =>
    array (
    ),
    12 => NULL,
  ),
));
$coverage->setTests(array (
  'ExampleTest::testOne' => 0,
));

$filter = $coverage->filter();
$filter->setWhitelistedFiles(array (
));

return $coverage;
