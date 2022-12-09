<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\ResponseAnalyzerInterface;
use Psr\Http\Message\ResponseInterface;

final class NoRedirectResponseAnalyzer implements ResponseAnalyzerInterface
{
    public function isAcceptable(ResponseInterface $response): bool
    {
        return $response->getStatusCode() < 300 || $response->getStatusCode() >= 400;
    }
}
