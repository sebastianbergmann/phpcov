<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCOV\TestFixture;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\PHPCOV\TestFixture\Greeter
 */
final class GreeterTest extends TestCase
{
    public function testGreetsWorld(): void
    {
        $this->assertSame('Hello world!', (new Greeter)->greetWorld());
    }

    public function testGreetsWithName(): void
    {
        $this->assertSame('Greetings, Professor Falken', (new Greeter)->greetWithName('Professor Falken'));
    }
}
