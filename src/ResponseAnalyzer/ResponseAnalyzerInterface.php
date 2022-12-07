<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer;

use Psr\Http\Message\ResponseInterface;

interface ResponseAnalyzerInterface
{
    public function isAcceptable(ResponseInterface $response): bool;
}
