<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use Mingalevme\RetryablePsrHttpClient\BackoffCalc\BackoffCalcInterface;
use Mingalevme\RetryablePsrHttpClient\BackoffCalc\ExponentialBackoffCalc;
use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\Http5xx429ResponseAnalyzer;
use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\ResponseAnalyzerInterface;
use Mingalevme\RetryablePsrHttpClient\Sleeper\PhpSleeper;
use Mingalevme\RetryablePsrHttpClient\Sleeper\SleeperInterface;
use Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit\RetryablePsrHttpClientTest;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @see RetryablePsrHttpClientTest
 */
final class RetryablePsrHttpClient implements ClientInterface
{
    private const DEFAULT_RETRY_COUNT = 3;
    private const DEFAULT_RESPECT_RETRY_AFTER_HEADER = true;
    private const MICROSECOND = 0.000001;

    private ClientInterface $psrHttpClient;

    /** @var int<1, max> */
    private int $retryCount;
    private BackoffCalcInterface $backoffCalc;
    private SleeperInterface $sleeper;
    private ResponseAnalyzerInterface $responseAnalyzer;
    /** @var list<EventListenerInterface> */
    private array $listeners;
    private bool $respectRetryAfterHeader;
    private ClockInterface $clock;

    public function __construct(ClientInterface $inner, ?Config $config = null)
    {
        $this->psrHttpClient = $inner;
        $this->retryCount = $config?->getRetryCount() ?: self::DEFAULT_RETRY_COUNT;
        $this->backoffCalc = $config?->getBackoffCalc() ?: new ExponentialBackoffCalc();
        $this->sleeper = $config?->getSleeper() ?: new PhpSleeper();
        $this->responseAnalyzer = $config?->getResponseAnalyzer() ?: new Http5xx429ResponseAnalyzer();
        $this->listeners = $config?->getEventListeners() ?: [];
        /** @psalm-suppress PossiblyNullReference */
        $this->respectRetryAfterHeader = ($config?->getRespectRetryAfterHeader() === null)
            ? self::DEFAULT_RESPECT_RETRY_AFTER_HEADER
            : (bool)$config->getRespectRetryAfterHeader();
        $this->clock = $config?->getClock() ?: new PhpClock();
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @throws ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        /** @var ResponseInterface|null $response */
        $response = null;
        /** @var ClientExceptionInterface|null $e */
        $e = null;
        for ($attemptNumber = 1; $attemptNumber <= $this->retryCount; $attemptNumber++) {
            $response = null;
            $e = null;
            if ($request->getBody()->tell() > 0 && $request->getBody()->isSeekable()) {
                $request->getBody()->rewind();
            }
            foreach ($this->listeners as $listener) {
                $listener->onRequest($attemptNumber, $request);
            }
            try {
                $response = $this->psrHttpClient->sendRequest($request);
            } catch (ClientExceptionInterface $e) {
            }
            if ($response) {
                foreach ($this->listeners as $listener) {
                    $listener->onResponse($attemptNumber, $request, $response);
                }
                if ($this->responseAnalyzer->isAcceptable($response)) {
                    return $response;
                }
                foreach ($this->listeners as $listener) {
                    $listener->onErrorResponse($attemptNumber, $request, $response);
                    $listener->onError($attemptNumber, $request, $response);
                }
            } else {
                foreach ($this->listeners as $listener) {
                    /** @psalm-suppress PossiblyNullArgument */
                    $listener->onException($attemptNumber, $request, $e);
                    /** @psalm-suppress PossiblyNullArgument */
                    $listener->onError($attemptNumber, $request, $e);
                }
            }
            if ($attemptNumber < $this->retryCount) {
                $this->sleep($attemptNumber, $response);
            }
        }
        if ($e) {
            throw $e;
        }
        /** @psalm-suppress NullableReturnStatement */
        return $response;
    }

    /**
     * @param int<1, max> $attemptNumber
     * @param ResponseInterface|null $response
     * @return void
     */
    private function sleep(int $attemptNumber, ?ResponseInterface $response): void
    {
        foreach ($this->getTimeouts($attemptNumber, $response) as $timeout) {
            $this->sleeper->sleep($timeout);
        }
    }

    /**
     * @param int<1, max> $attemptNumber
     * @param ResponseInterface|null $response
     * @return Generator<int<1, max>|float>
     */
    private function getTimeouts(int $attemptNumber, ?ResponseInterface $response): Generator
    {
        $timeoutFromHeader = 0.0;
        if ($response && $this->respectRetryAfterHeader) {
            foreach ($this->getTimeoutsFromResponse($response) as $timeout) {
                $timeoutFromHeader += $timeout;
                yield $timeout;
            }
        }
        if ($timeoutFromHeader < self::MICROSECOND) {
            yield from [$this->backoffCalc->calculate($attemptNumber)];
        }
    }

    /**
     * @param ResponseInterface $response
     * @return Generator<int<1, max>|float>
     */
    private function getTimeoutsFromResponse(ResponseInterface $response): Generator
    {
        $wasAnyTimeouts = false;
        foreach ($response->getHeader('Retry-After') as $value) {
            if (is_numeric($value)) {
                $timeout = intval($value);
                if ($timeout > 0) {
                    $wasAnyTimeouts = true;
                    yield $timeout;
                }
            } else {
                $timestamp = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC1123, $value);
                if ($timestamp) {
                    $diff = $this->getTimestampDiffNow($timestamp);
                    if ($diff >= self::MICROSECOND) {
                        $wasAnyTimeouts = true;
                        yield $diff;
                    }
                }
            }
        }
        if (!$wasAnyTimeouts) {
            yield from [];
        }
    }

    private function getTimestampDiffNow(DateTimeImmutable $retryAfter): float
    {
        $now = $this->clock->now();
        return (float)$now->diff($retryAfter)->format('%r%s.%f');
    }
}
