<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class BrokenClock implements ClockInterface
{
    private DateTimeImmutable $timestamp;

    public function __construct(DateTimeImmutable $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    #[\Override]
    public function now(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
