<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractEventListener implements EventListenerInterface
{
    public function onRequest(
        int $attemptNumber,
        RequestInterface $request,
    ): void {
    }

    public function onException(
        int $attemptNumber,
        RequestInterface $request,
        ClientExceptionInterface $exception,
    ): void {
    }

    public function onResponse(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
    }

    public function onSuccess(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
    }

    public function onErrorResponse(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
    }

    public function onError(
        int $attemptNumber,
        RequestInterface $request,
        ClientExceptionInterface|ResponseInterface $error,
    ): void {
    }
}
