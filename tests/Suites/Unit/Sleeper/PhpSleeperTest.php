<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\Sleeper;

use Mingalevme\RetryablePsrHttpClient\Sleeper\PhpSleeper;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

/**
 * @see PhpSleeper
 */
final class PhpSleeperTest extends TestCase
{
    public function test(): void
    {
        $sleeper = new PhpSleeper();
        $t0 = microtime(true);
        $sleeper->sleep(0.000010);
        $t1 = microtime(true);
        self::assertTrue($t1 - $t0 > 0.000009);
        self::assertTrue($t1 - $t0 < 0.001);
    }
}
