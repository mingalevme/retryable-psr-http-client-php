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
    public function sleep(int|float $timeout): void
    {
        if ($timeout < 0.000001) {
            return;
        }
        $timeoutUs = abs(intval($timeout * 1_000_000));
        usleep($timeoutUs);
    }
}
