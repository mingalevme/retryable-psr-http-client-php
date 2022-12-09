<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\BackoffCalc;

use InvalidArgumentException;
use Mingalevme\RetryablePsrHttpClient\BackoffCalc\LinearBackoffCalc;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

final class LinearBackoffCalcTest extends TestCase
{
    public function test(): void
    {
        $calc = new LinearBackoffCalc(3, 1);
        self::assertSame(4, $calc->calculate(1));
        self::assertSame(7, $calc->calculate(2));
        self::assertSame(10, $calc->calculate(3));
    }

    public function testNegativeSlope(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LinearBackoffCalc(-1.0);
    }

    public function testNegativeInitVal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LinearBackoffCalc(0, -1.0);
    }
}
