<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace fixture\example2\src;

final class Greeter2
{
    public const GREETING = 'Hello world!';

    public function greetWorld(): string
    {
        return self::GREETING;
    }
}
