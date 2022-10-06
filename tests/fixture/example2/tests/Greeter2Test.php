<?php

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
        $this->assertSame(Greeter2::GREETING, (new Greeter2())->greetWorld());
    }
}
