<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\BackoffCalc;

use InvalidArgumentException;
use Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\BackoffCalc\ConstBackoffCalcTest;

/**
 * @see ConstBackoffCalcTest
 */
final class ConstBackoffCalc implements BackoffCalcInterface
{
    /** @var int<0, max>|float */
    private int|float $timeout;

    /**
     * @param int<0, max>|float $timeout Seconds
     */
    public function __construct(int|float $timeout)
    {
        if ((float)$timeout < 0.0) {
            throw new InvalidArgumentException('Timeout must be non-negative');
        }
        $this->timeout = $timeout;
    }

    public function calculate(int $attemptNumber): int|float
    {
        return $this->timeout;
    }
}
