<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\Sleeper;

interface SleeperInterface
{
    /**
     * @param int<0, max>|float $timeout Seconds
     * @return void
     */
    public function sleep(int|float $timeout): void;
}
