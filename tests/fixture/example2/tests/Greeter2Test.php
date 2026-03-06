<?php declare(strict_types=1);
/*
 * This file is part of phpcov.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace fixture\example2\tests;

use fixture\example2\src\Greeter2;
use PHPUnit\Framework\TestCase;

/**
 * @covers \fixture\example2\src\Greeter2
 */
final class Greeter2Test extends TestCase
{
    public function testGreetsWorld(): void
    {
        $this->assertSame(Greeter2::GREETING, (new Greeter2)->greetWorld());
    }
}
