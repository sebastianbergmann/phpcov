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

use function array_values;
use function explode;
use function fclose;
use function implode;
use function is_resource;
use function proc_close;
use function proc_open;
use function realpath;
use function rewind;
use function sprintf;
use function stream_get_contents;
use function substr;
use function tmpfile;
use function trim;

/**
 * The Git repository that a directory belongs to.
 */
final readonly class Git
{
    /**
     * @var non-empty-string
     */
    private string $repository;

    /**
     * @throws GitException
     */
    public function __construct(string $directory)
    {
        try {
            $repository = realpath(trim($this->run($directory, 'rev-parse', '--show-toplevel')));
        } catch (GitException $e) {
            throw new GitException(
                sprintf(
                    'Unable to determine the Git repository that "%s" belongs to: %s',
                    $directory,
                    $e->getMessage(),
                ),
                previous: $e,
            );
        }

        if ($repository === false) {
            // @codeCoverageIgnoreStart
            throw new GitException(
                sprintf(
                    'Unable to determine the Git repository that "%s" belongs to',
                    $directory,
                ),
            );
            // @codeCoverageIgnoreEnd
        }

        $this->repository = $repository;
    }

    /**
     * @return non-empty-string
     */
    public function repository(): string
    {
        return $this->repository;
    }

    /**
     * The default branch of the "origin" remote, which is also what Infection
     * compares with by default for its --git-diff-lines option. HEAD when
     * that reference does not exist, in a repository without a remote for
     * instance: then only uncommitted changes are compared.
     *
     * @return non-empty-string
     */
    public function defaultRevision(): string
    {
        try {
            $revision = trim($this->run($this->repository, 'symbolic-ref', '--short', 'refs/remotes/origin/HEAD'));
        } catch (GitException) {
            return 'HEAD';
        }

        if ($revision === '') {
            // @codeCoverageIgnoreStart
            return 'HEAD';
            // @codeCoverageIgnoreEnd
        }

        return $revision;
    }

    /**
     * The commit that HEAD and a revision have in common. Comparing with it
     * instead of with the revision itself leaves out what was changed in the
     * revision since HEAD branched off from it.
     *
     * @throws GitException
     *
     * @return non-empty-string
     */
    public function mergeBase(string $revision): string
    {
        try {
            $mergeBase = trim($this->run($this->repository, 'merge-base', 'HEAD', $revision));
        } catch (GitException $e) {
            throw new GitException(
                sprintf(
                    'Unable to determine the merge base of HEAD and "%s": %s',
                    $revision,
                    $e->getMessage(),
                ),
                previous: $e,
            );
        }

        if ($mergeBase === '') {
            // @codeCoverageIgnoreStart
            throw new GitException(
                sprintf(
                    'Unable to determine the merge base of HEAD and "%s"',
                    $revision,
                ),
            );
            // @codeCoverageIgnoreEnd
        }

        return $mergeBase;
    }

    /**
     * The difference between a commit and the working tree, which includes
     * staged and unstaged changes, as a unified diff.
     *
     * The options make the output independent of how Git is configured: of
     * an external diff tool, of filters that convert files to text, of the
     * prefixes of the paths, and of whether paths are relative to the
     * current working directory, for instance. Files that were deleted have
     * no lines that could be covered.
     *
     * @throws GitException
     */
    public function diff(string $commit): string
    {
        return $this->run(
            $this->repository,
            'diff',
            '--unified=0',
            '--no-color',
            '--no-ext-diff',
            '--no-textconv',
            '--no-relative',
            '--find-renames',
            '--src-prefix=a/',
            '--dst-prefix=b/',
            '--diff-filter=d',
            $commit,
            '--',
        );
    }

    /**
     * The files that are neither tracked nor ignored. These files are not
     * part of the difference between a commit and the working tree.
     *
     * @throws GitException
     *
     * @return list<non-empty-string>
     */
    public function untrackedFiles(): array
    {
        $files = [];

        foreach (explode("\0", $this->run($this->repository, 'ls-files', '--others', '--exclude-standard', '-z')) as $file) {
            if ($file !== '') {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * @throws GitException
     */
    private function run(string $directory, string ...$arguments): string
    {
        $stderr = tmpfile();

        if ($stderr === false) {
            // @codeCoverageIgnoreStart
            throw new GitException('Unable to create temporary file');
            // @codeCoverageIgnoreEnd
        }

        $pipes = [];

        // The paths that Git prints are not quoted, and the output of Git is
        // not written to a pipe that Git could block on while this process
        // waits for its other output
        $process = @proc_open(
            ['git', '-c', 'core.quotePath=false', ...array_values($arguments)],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => $stderr,
            ],
            $pipes,
            $directory,
        );

        if (!is_resource($process)) {
            fclose($stderr);

            throw new GitException('Git could not be run');
        }

        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);

        fclose($pipes[1]);

        $exitCode = proc_close($process);

        rewind($stderr);

        // Only the first line of what Git prints is the error message
        $error = trim(explode("\n", trim((string) stream_get_contents($stderr)))[0]);

        fclose($stderr);

        if ($exitCode !== 0 || $output === false) {
            if ($error === '') {
                $error = sprintf(
                    '"git %s" failed with exit code %d',
                    implode(' ', $arguments),
                    $exitCode,
                );
            }

            if (substr($error, 0, 7) === 'fatal: ') {
                $error = substr($error, 7);
            }

            throw new GitException($error);
        }

        return $output;
    }
}
