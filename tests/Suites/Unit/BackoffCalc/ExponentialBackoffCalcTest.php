<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\BackoffCalc;

use InvalidArgumentException;
use Mingalevme\RetryablePsrHttpClient\BackoffCalc\ExponentialBackoffCalc;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

final class ExponentialBackoffCalcTest extends TestCase
{
    public function test(): void
    {
        $calc = new ExponentialBackoffCalc(3);
        self::assertSame(1, $calc->calculate(1));
        self::assertSame(3, $calc->calculate(2));
        self::assertSame(9, $calc->calculate(3));
    }

    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ExponentialBackoffCalc(-1.0);
    }
}
