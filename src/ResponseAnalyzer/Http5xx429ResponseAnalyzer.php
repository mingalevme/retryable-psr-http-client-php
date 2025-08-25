<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer;

use Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\Http5xx429ResponseAnalyzerTest;
use Psr\Http\Message\ResponseInterface;

/**
 * @see Http5xx429ResponseAnalyzerTest
 */
final class Http5xx429ResponseAnalyzer implements ResponseAnalyzerInterface
{
    #[\Override]
    public function isAcceptable(ResponseInterface $response): bool
    {
        return $response->getStatusCode() < 500 && $response->getStatusCode() !== 429;
    }
}
