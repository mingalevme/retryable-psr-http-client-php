<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\BackoffCalc;

use InvalidArgumentException;

final class ExponentialBackoffCalc implements BackoffCalcInterface
{
    private const DEFAULT_BASE = 2;

    /** @var positive-int|float */
    private int|float $base;

    /**
     * @param positive-int|float|null $base
     */
    public function __construct(int|float|null $base = self::DEFAULT_BASE)
    {
        if ($base === null) {
            $this->base = self::DEFAULT_BASE;
            return;
        }
        if ($base <= 0.0) {
            throw new InvalidArgumentException('Base must be positive');
        }
        $this->base = $base;
    }

    #[\Override]
    public function calculate(int $attemptNumber): int|float
    {
        return abs($this->base ** ($attemptNumber - 1));
    }
}
