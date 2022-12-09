<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface EventListenerInterface
{
    /**
     * @param positive-int $attemptNumber
     */
    public function onRequest(int $attemptNumber, RequestInterface $request): void;

    /**
     * @param positive-int $attemptNumber
     */
    public function onException(
        int $attemptNumber,
        RequestInterface $request,
        ClientExceptionInterface $exception,
    ): void;

    /**
     * @param positive-int $attemptNumber
     */
    public function onResponse(int $attemptNumber, RequestInterface $request, ResponseInterface $response): void;

    /**
     * @param positive-int $attemptNumber
     */
    public function onErrorResponse(int $attemptNumber, RequestInterface $request, ResponseInterface $response): void;

    /**
     * @param positive-int $attemptNumber
     */
    public function onError(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface|ClientExceptionInterface $error,
    ): void;
}
