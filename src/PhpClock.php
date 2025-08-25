<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use DateTimeImmutable;
use Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\PhpClockTest;
use Psr\Clock\ClockInterface;

/**
 * @see PhpClockTest
 */
final class PhpClock implements ClockInterface
{
    #[\Override]
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
