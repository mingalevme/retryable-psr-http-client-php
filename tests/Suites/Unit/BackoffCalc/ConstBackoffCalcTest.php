<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\BackoffCalc;

use Mingalevme\RetryablePsrHttpClient\BackoffCalc\ConstBackoffCalc;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

final class ConstBackoffCalcTest extends TestCase
{
    public function test(): void
    {
        $calc = new ConstBackoffCalc(5);
        self::assertSame(5, $calc->calculate(1));
        self::assertSame(5, $calc->calculate(2));
        self::assertSame(5, $calc->calculate(99));
    }
}
