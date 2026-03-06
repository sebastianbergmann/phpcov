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
    private ?string $command;
    private ?string $script;
    private ?string $directory;
    private ?string $coverage;
    private ?string $patch;
    private ?string $configuration;

    /**
     * @var list<non-empty-string>
     */
    private array $include;
    private bool $pathCoverage;
    private bool $addUncovered;
    private ?string $clover;
    private ?string $cobertura;
    private ?string $crap4j;
    private ?string $html;
    private ?string $php;
    private ?string $text;
    private ?string $xml;
    private bool $help;
    private bool $version;
    private ?string $pathPrefix;

    /**
     * @param list<non-empty-string> $include
     */
    public function __construct(?string $command, ?string $script, ?string $directory, ?string $coverage, ?string $patch, ?string $configuration, array $include, bool $pathCoverage, bool $addUncovered, ?string $clover, ?string $cobertura, ?string $crap4j, ?string $html, ?string $php, ?string $text, ?string $xml, ?string $pathPrefix, bool $help, bool $version)
    {
        $this->command       = $command;
        $this->script        = $script;
        $this->directory     = $directory;
        $this->coverage      = $coverage;
        $this->patch         = $patch;
        $this->configuration = $configuration;
        $this->include       = $include;
        $this->pathCoverage  = $pathCoverage;
        $this->addUncovered  = $addUncovered;
        $this->clover        = $clover;
        $this->cobertura     = $cobertura;
        $this->crap4j        = $crap4j;
        $this->html          = $html;
        $this->php           = $php;
        $this->text          = $text;
        $this->xml           = $xml;
        $this->pathPrefix    = $pathPrefix;
        $this->help          = $help;
        $this->version       = $version;
    }

    public function command(): ?string
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

    /**
     * @return list<non-empty-string>
     */
    public function include(): array
    {
        return $this->include;
    }

    public function pathCoverage(): bool
    {
        return $this->pathCoverage;
    }

    public function addUncovered(): bool
    {
        return $this->addUncovered;
    }

    public function clover(): ?string
    {
        return $this->clover;
    }

    public function cobertura(): ?string
    {
        return $this->cobertura;
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

    public function pathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function help(): bool
    {
        return $this->help;
    }

    public function version(): bool
    {
        return $this->version;
    }

    public function reportConfigured(): bool
    {
        return $this->clover() !== null ||
               $this->cobertura() !== null ||
               $this->crap4j() !== null ||
               $this->html() !== null ||
               $this->php() !== null ||
               $this->text() !== null ||
               $this->xml() !== null;
    }
}
