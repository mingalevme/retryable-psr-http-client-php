<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\BackoffCalc;

use InvalidArgumentException;

final class LinearBackoffCalc implements BackoffCalcInterface
{
    private const DEFAULT_SLOPE = 1.0;
    private const DEFAULT_INIT_VALUE = 0.0;

    private int|float $slope;
    private int|float $initValue;

    /**
     * @param positive-int|float|null $slope Only positive values
     * @param positive-int|float|null $initValue Seconds
     */
    public function __construct(
        int|float|null $slope = self::DEFAULT_SLOPE,
        int|float|null $initValue = self::DEFAULT_INIT_VALUE,
    ) {
        if ($slope !== null && (float)$slope < 0.0) {
            throw new InvalidArgumentException('Slope must be positive');
        }
        $this->slope = $slope ?: self::DEFAULT_SLOPE;
        if ($initValue !== null && (float)$initValue < 0.0) {
            throw new InvalidArgumentException('Initial value must be non-negative');
        }
        $this->initValue = $initValue ?: self::DEFAULT_INIT_VALUE;
    }

    public function calculate(int $attemptNumber): int|float
    {
        return $this->initValue + $attemptNumber * $this->slope;
    }
}
