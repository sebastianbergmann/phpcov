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

final class Greeter
{
    public function greetWorld(): string
    {
        return 'Hello world!';
    }

    public function greetWithName(string $name): string
    {
        return 'Greetings, ' . $name;
    }
}
