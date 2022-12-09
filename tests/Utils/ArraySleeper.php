<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use Mingalevme\RetryablePsrHttpClient\Sleeper\SleeperInterface;

final class ArraySleeper implements SleeperInterface
{
    /** @var list<int<0, max>|float> */
    private array $sleeps = [];

    public function sleep(int|float $timeout): void
    {
        $this->sleeps[] = $timeout;
    }

    /**
     * @return list<int<0, max>|float>
     */
    public function getSleeps(): array
    {
        return $this->sleeps;
    }

    public function clear(): void
    {
        $this->sleeps = [];
    }
}
