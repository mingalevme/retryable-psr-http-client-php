<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use Mingalevme\RetryablePsrHttpClient\EventListenerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ArrayEventListener implements EventListenerInterface
{
    /** @var list<array{int, RequestInterface}> */
    private array $onRequest = [];
    /** @var list<array{int, ClientExceptionInterface}> */
    private array $onException = [];
    /** @var list<array{int, ResponseInterface}> */
    private array $onResponse = [];
    /** @var list<array{int, ResponseInterface}> */
    private array $onErrorResponse = [];

    public function onRequest(int $attemptCount, RequestInterface $request): void
    {
        $this->onRequest[] = [$attemptCount, $request];
    }

    public function onException(int $attemptCount, ClientExceptionInterface $exception): void
    {
        $this->onException[] = [$attemptCount, $exception];
    }

    public function onResponse(int $attemptCount, ResponseInterface $response): void
    {
        $this->onResponse[] = [$attemptCount, $response];
    }

    public function onErrorResponse(int $attemptCount, ResponseInterface $response): void
    {
        $this->onErrorResponse[] = [$attemptCount, $response];
    }

    /**
     * @return list<array{int, RequestInterface}>
     */
    public function getOnRequest(): array
    {
        return $this->onRequest;
    }

    /**
     * @return list<array{int, ClientExceptionInterface}>
     */
    public function getOnException(): array
    {
        return $this->onException;
    }

    /**
     * @return list<array{int, ResponseInterface}>
     */
    public function getOnResponse(): array
    {
        return $this->onResponse;
    }

    /**
     * @return list<array{int, ResponseInterface}>
     */
    public function getOnErrorResponse(): array
    {
        return $this->onErrorResponse;
    }

    public function clear(): void
    {
        $this->onRequest = [];
        $this->onException = [];
        $this->onResponse = [];
        $this->onErrorResponse = [];
    }
}
