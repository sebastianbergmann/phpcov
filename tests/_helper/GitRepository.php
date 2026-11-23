<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCOV\TestHelper;

use const DIRECTORY_SEPARATOR;
use function array_map;
use function chmod;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_put_contents;
use function implode;
use function is_dir;
use function is_link;
use function mkdir;
use function putenv;
use function realpath;
use function rmdir;
use function scandir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function time;
use function touch;
use function trim;
use function uniqid;
use function unlink;
use DateTimeImmutable;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

/**
 * A Git repository in a temporary directory, and code coverage data for
 * files in its src directory.
 *
 * The configuration of Git of the user that runs the tests is not used.
 */
final class GitRepository
{
    private readonly string $directory;
    private readonly string $configuration;

    /**
     * @var list<string>
     */
    private array $files = [];

    /**
     * The source code of a class with two methods that each have one
     * executable line: line 8 and line 13.
     */
    public static function greeter(string $greetingForWorld = 'Hello world!', string $greetingForName = 'Greetings, '): string
    {
        return sprintf(
            <<<'EOT'
            <?php declare(strict_types=1);
            namespace Example;

            final class Greeter
            {
                public function greetWorld(): string
                {
                    return '%s';
                }

                public function greetWithName(string $name): string
                {
                    return '%s' . $name;
                }
            }

            EOT,
            $greetingForWorld,
            $greetingForName,
        );
    }

    /**
     * The source code of a class with a method that has one statement, which
     * spans lines 8 to 12. A comment can be added in line 13.
     */
    public static function farewell(string $farewell = 'Bye', string $comment = ''): string
    {
        return sprintf(
            <<<'EOT'
            <?php declare(strict_types=1);
            namespace Example;

            final class Farewell
            {
                public function bye(string $name): string
                {
                    return sprintf(
                        '%%s, %%s',
                        '%s',
                        $name,
                    );
                    %s
                }
            }

            EOT,
            $farewell,
            $comment,
        );
    }

    public function __construct()
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('phpcov');

        mkdir($directory);

        $this->directory     = realpath($directory);
        $this->configuration = $this->temporaryFile();

        putenv('GIT_CONFIG_GLOBAL=' . $this->configuration);
        putenv('GIT_CONFIG_NOSYSTEM=1');

        $this->git('init', '--quiet', '--initial-branch=main');
        $this->git('config', 'user.name', 'phpcov');
        $this->git('config', 'user.email', 'phpcov@example.org');
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function write(string $file, string $contents): void
    {
        $file = $this->directory . DIRECTORY_SEPARATOR . $file;

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), recursive: true);
        }

        file_put_contents($file, $contents);
    }

    /**
     * Sets the time a file was last modified to a time that is relative to
     * now.
     */
    public function touch(string $file, int $seconds): void
    {
        touch($this->directory . DIRECTORY_SEPARATOR . $file, time() + $seconds);
    }

    public function commit(): void
    {
        $this->git('add', '--all');
        $this->git('commit', '--quiet', '--message=Commit');
    }

    public function git(string ...$arguments): string
    {
        $output   = [];
        $exitCode = 0;

        exec(
            sprintf(
                'git -C %s %s 2>&1',
                escapeshellarg($this->directory),
                implode(' ', array_map(escapeshellarg(...), $arguments)),
            ),
            $output,
            $exitCode,
        );

        if ($exitCode !== 0) {
            throw new RuntimeException(implode("\n", $output));
        }

        return trim(implode("\n", $output));
    }

    /**
     * Writes a file with code coverage data for files in the src directory
     * of the repository and returns the path to that file, which is not
     * located in the repository. Unless configured otherwise, the data
     * states that it was written now.
     *
     * @param array<string, array<int, ?list<string>>> $lineCoverage
     */
    public function coverage(array $lineCoverage, ?string $basePath = null, ?string $timestamp = null): string
    {
        $data = (new Unserializer)->unserialize(
            dirname(__DIR__) . '/fixture/example/coverage/testGreetsWorld.cov',
        );

        $data['buildInformation']['timestamp'] = $timestamp ?? (new DateTimeImmutable)->format('D M j G:i:s T Y');

        $data['basePath']     = $basePath ?? $this->directory . DIRECTORY_SEPARATOR . 'src';
        $data['codeCoverage'] = new ProcessedCodeCoverageData;

        $data['codeCoverage']->setLineCoverage($lineCoverage);

        $file = $this->temporaryFile();

        (new Serializer)->write($file, $data);

        return $file;
    }

    public function delete(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }

        $this->deleteDirectory($this->directory);
    }

    private function temporaryFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'phpcov');

        $this->files[] = $file;

        return $file;
    }

    private function deleteDirectory(string $directory): void
    {
        foreach (scandir($directory) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($path) && !is_link($path)) {
                $this->deleteDirectory($path);

                continue;
            }

            @chmod($path, 0o644);
            unlink($path);
        }

        rmdir($directory);
    }
}
