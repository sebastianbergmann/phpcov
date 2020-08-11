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

final class Arguments
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var ?string
     */
    private $script;

    /**
     * @var ?string
     */
    private $directory;

    /**
     * @var ?string
     */
    private $coverage;

    /**
     * @var ?string
     */
    private $patch;

    /**
     * @var ?string
     */
    private $configuration;

    /**
     * @var string[]
     */
    private $include;

    /**
     * @var bool
     */
    private $addUncovered;

    /**
     * @var bool
     */
    private $processUncovered;

    /**
     * @var ?string
     */
    private $clover;

    /**
     * @var ?string
     */
    private $crap4j;

    /**
     * @var ?string
     */
    private $html;

    /**
     * @var ?string
     */
    private $php;

    /**
     * @var ?string
     */
    private $text;

    /**
     * @var ?string
     */
    private $xml;

    /**
     * @var bool
     */
    private $help;

    /**
     * @var ?string
     */
    private $pathPrefix;

    public function __construct(string $command, ?string $script, ?string $directory, ?string $coverage, ?string $patch, ?string $configuration, array $include, bool $addUncovered, bool $processUncovered, ?string $clover, ?string $crap4j, ?string $html, ?string $php, ?string $text, ?string $xml, bool $help, ?string $pathPrefix)
    {
        $this->command          = $command;
        $this->script           = $script;
        $this->directory        = $directory;
        $this->coverage         = $coverage;
        $this->patch            = $patch;
        $this->configuration    = $configuration;
        $this->include          = $include;
        $this->addUncovered     = $addUncovered;
        $this->processUncovered = $processUncovered;
        $this->clover           = $clover;
        $this->crap4j           = $crap4j;
        $this->html             = $html;
        $this->php              = $php;
        $this->text             = $text;
        $this->xml              = $xml;
        $this->help             = $help;
        $this->pathPrefix       = $pathPrefix;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function script(): ?string
    {
        return $this->script;
    }

    public function directory(): ?string
    {
        return $this->directory;
    }

    public function coverage(): ?string
    {
        return $this->coverage;
    }

    public function patch(): ?string
    {
        return $this->patch;
    }

    public function configuration(): ?string
    {
        return $this->configuration;
    }

    public function include(): array
    {
        return $this->include;
    }

    public function addUncovered(): bool
    {
        return $this->addUncovered;
    }

    public function processUncovered(): bool
    {
        return $this->processUncovered;
    }

    public function clover(): ?string
    {
        return $this->clover;
    }

    public function crap4j(): ?string
    {
        return $this->crap4j;
    }

    public function html(): ?string
    {
        return $this->html;
    }

    public function php(): ?string
    {
        return $this->php;
    }

    public function text(): ?string
    {
        return $this->text;
    }

    public function xml(): ?string
    {
        return $this->xml;
    }

    public function help(): bool
    {
        return $this->help;
    }

    public function pathPrefix(): string
    {
        return $this->pathPrefix;
    }
}
