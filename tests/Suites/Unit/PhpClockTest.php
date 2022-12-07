<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit;

use Mingalevme\RetryablePsrHttpClient\PhpClock;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

/**
 * @see PhpClock
 */
final class PhpClockTest extends TestCase
{
    public function test(): void
    {
        $clock = new PhpClock();
        $t1 = floatval($clock->now()->format('U.u'));
        $t2 = microtime(true);
        self::assertLessThan(0.000010, $t1 - $t2);
    }
}
