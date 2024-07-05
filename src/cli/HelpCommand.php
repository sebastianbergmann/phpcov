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

final class HelpCommand extends Command
{
    public function run(Arguments $arguments): int
    {
        print <<<'EOT'
Usage:
  phpcov execute        [options] <script>
  phpcov merge          [options] <directory with *.cov files>
  phpcov patch-coverage [options] <coverage file> <patch file>

Options for "phpcov execute":

  --configuration <file> Load PHPUnit configuration from XML configuration
  --include <directory>  Include <directory> in code coverage analysis
  --path-coverage        Perform path coverage analysis
  --add-uncovered        Include uncovered files in code coverage report
  --process-uncovered    Process uncovered file for code coverage report

Options common for both "phpcov execute" and "phpcov merge":

  --clover <file>        Generate code coverage report in Clover XML format
  --cobertura <file>     Generate code coverage report in Cobertura XML format
  --crap4j <file>        Generate code coverage report in Crap4J XML format
  --html <directory>     Generate code coverage report in HTML format
  --php <file>           Export php-code-coverage object
  --text <file>          Generate code coverage report in text format
  --xml <directory>      Generate code coverage report in PHPUnit XML format

Options for "phpcov patch-coverage":

  --path-prefix <prefix> Prefix that needs to be stripped from paths in the patch

EOT;

        return 0;
    }
}
