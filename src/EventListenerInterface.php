<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface EventListenerInterface
{
    public function onRequest(int $attemptCount, RequestInterface $request): void;

    public function onException(int $attemptCount, ClientExceptionInterface $exception): void;

    public function onResponse(int $attemptCount, ResponseInterface $response): void;

    public function onErrorResponse(int $attemptCount, ResponseInterface $response): void;
}
