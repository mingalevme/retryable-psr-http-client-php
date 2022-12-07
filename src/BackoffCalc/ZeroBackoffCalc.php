<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\BackoffCalc;

final class ZeroBackoffCalc implements BackoffCalcInterface
{
    public function calculate(int $attemptNumber): int
    {
        return 0;
    }
}
