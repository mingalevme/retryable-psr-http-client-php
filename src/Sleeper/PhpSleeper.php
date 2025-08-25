<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\Sleeper;

use Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\Sleeper\PhpSleeperTest;

/**
 * @see PhpSleeperTest
 * @codeCoverageIgnore
 */
final class PhpSleeper implements SleeperInterface
{
    #[\Override]
    public function sleep(int|float $timeout): void
    {
        if (floatval($timeout) < 0.000001) {
            return;
        }
        $timeoutUs = is_int($timeout)
            ? abs($timeout * 1_000_000)
            : abs(intval($timeout * 1_000_000.0));
        usleep($timeoutUs);
    }
}
