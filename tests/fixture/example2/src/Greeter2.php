<?php

namespace fixture\example2\src;

final class Greeter2
{
    public const GREETING='Hello world!';

    public function greetWorld(): string
    {
        return self::GREETING;
    }
}