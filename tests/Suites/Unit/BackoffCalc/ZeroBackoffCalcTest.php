<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\BackoffCalc;

use Mingalevme\RetryablePsrHttpClient\BackoffCalc\ZeroBackoffCalc;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

final class ZeroBackoffCalcTest extends TestCase
{
    public function test(): void
    {
        $calc = new ZeroBackoffCalc();
        self::assertSame(0, $calc->calculate(1));
        self::assertSame(0, $calc->calculate(2));
        self::assertSame(0, $calc->calculate(99));
    }
}
