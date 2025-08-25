<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use DateTimeImmutable;
use Generator;
use Psr\Clock\ClockInterface;

final class ListOfValuesClock implements ClockInterface
{
    /** @var Generator<int, DateTimeImmutable> */
    private Generator $values;

    /**
     * @param iterable<int, DateTimeImmutable> $lizt
     */
    public function __construct(iterable $lizt)
    {
        $this->values = $this->iterableToGenerator($lizt);
    }

    #[\Override]
    public function now(): DateTimeImmutable
    {
        /** @psalm-var DateTimeImmutable $v */
        $v = $this->values->current();
        $this->values->next();
        return $v;
    }

    /**
     * @param iterable<int, DateTimeImmutable> $lizt
     * @return Generator<int, DateTimeImmutable>
     */
    private function iterableToGenerator(iterable $lizt): Generator
    {
        yield from $lizt;
    }
}
