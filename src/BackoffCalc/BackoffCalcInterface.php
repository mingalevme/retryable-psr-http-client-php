<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\BackoffCalc;

interface BackoffCalcInterface
{
    /**
     * @param positive-int $attemptNumber
     * @return int<0, max>|float Seconds
     */
    public function calculate(int $attemptNumber): int|float;
}
