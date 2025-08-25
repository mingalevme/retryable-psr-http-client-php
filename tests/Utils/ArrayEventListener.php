<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use Mingalevme\RetryablePsrHttpClient\EventListenerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ArrayEventListener implements EventListenerInterface
{
    /** @var list<array{positive-int, RequestInterface}> */
    private array $onRequest = [];
    /** @var list<array{positive-int, RequestInterface, ClientExceptionInterface}> */
    private array $onException = [];
    /** @var list<array{positive-int, RequestInterface, ResponseInterface}> */
    private array $onResponse = [];
    /** @var list<array{positive-int, RequestInterface, ResponseInterface}> */
    private array $onSuccess = [];
    /** @var list<array{positive-int, RequestInterface, ResponseInterface}> */
    private array $onErrorResponse = [];
    /** @var list<array{positive-int, RequestInterface, ResponseInterface|ClientExceptionInterface}> */
    private array $onError = [];

    #[\Override]
    public function onRequest(
        int $attemptNumber,
        RequestInterface $request,
    ): void {
        $this->onRequest[] = [$attemptNumber, $request];
    }

    #[\Override]
    public function onException(
        int $attemptNumber,
        RequestInterface $request,
        ClientExceptionInterface $exception,
    ): void {
        $this->onException[] = [$attemptNumber, $request, $exception];
    }

    #[\Override]
    public function onResponse(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
        $this->onResponse[] = [$attemptNumber, $request, $response];
    }


    #[\Override]
    public function onSuccess(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
        $this->onSuccess[] = [$attemptNumber, $request, $response];
    }

    #[\Override]
    public function onErrorResponse(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface $response,
    ): void {
        $this->onErrorResponse[] = [$attemptNumber, $request, $response];
    }

    #[\Override]
    public function onError(
        int $attemptNumber,
        RequestInterface $request,
        ResponseInterface|ClientExceptionInterface $error,
    ): void {
        $this->onError[] = [$attemptNumber, $request, $error];
    }

    /**
     * @return list<array{int, RequestInterface}>
     */
    public function getOnRequest(): array
    {
        return $this->onRequest;
    }

    /**
     * @return list<array{int, RequestInterface, ClientExceptionInterface}>
     */
    public function getOnException(): array
    {
        return $this->onException;
    }

    /**
     * @return list<array{int, RequestInterface, ResponseInterface}>
     */
    public function getOnResponse(): array
    {
        return $this->onResponse;
    }

    /**
     * @return list<array{int, RequestInterface, ResponseInterface}>
     */
    public function getOnSuccess(): array
    {
        return $this->onSuccess;
    }

    /**
     * @return list<array{int, RequestInterface, ResponseInterface}>
     */
    public function getOnErrorResponse(): array
    {
        return $this->onErrorResponse;
    }

    /**
     * @return list<array{int, RequestInterface, ResponseInterface|ClientExceptionInterface}>
     */
    public function getOnError(): array
    {
        return $this->onError;
    }

    public function clear(): void
    {
        $this->onRequest = [];
        $this->onException = [];
        $this->onResponse = [];
        $this->onSuccess = [];
        $this->onErrorResponse = [];
        $this->onError = [];
    }
}
