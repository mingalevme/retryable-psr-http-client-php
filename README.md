# mingalevme/retryable-psr-http-client-php

[![quality](https://github.com/mingalevme/retryable-psr-http-client-php/actions/workflows/quality.yml/badge.svg)](https://github.com/mingalevme/retryable-psr-http-client-php/actions)
[![codecov](https://codecov.io/gh/mingalevme/retryable-psr-http-client-php/branch/master/graph/badge.svg?token=JelfrDfOkJ)](https://codecov.io/gh/mingalevme/retryable-psr-http-client-php)
[![version](https://img.shields.io/packagist/v/mingalevme/retryable-psr-http-client)](https://packagist.org/packages/mingalevme/retryable-psr-http-client)
[![license](https://img.shields.io/packagist/l/mingalevme/retryable-psr-http-client)](https://packagist.org/packages/mingalevme/retryable-psr-http-client)

Simple Retryable Psr Http Client Decorator with **Retry-After**-header support* and 100% code coverage.

> **_NOTE:_**  **Retry-After**-header handling is disabled by default
> because relying on untrusted headers it risky and dangerous,
> turn it on only if you clearly understand the consequences.

**Composer**

```shell
composer require mingalevme/retryable-psr-http-client
```

**Example 1 (Simple drop-in replacement)**

- Max **3** attempts.
- Triggers on **5xx**/**429** response status codes and/or `Psr\Http\Client\ClientExceptionInterface`.
- Exponential backoff: 2 power of attempt number (1, 2, 4, 8, ...).

```shell
use Mingalevme\RetryablePsrHttpClient\RetryablePsrHttpClient;
use Psr\Http\Client\ClientInterface;

$someDiContainer->decorate(ClientInterface::class, function (ClientInterface $client): RetryablePsrHttpClient {
    return new RetryablePsrHttpClient($client);
});
```

**Example 2 (Extended Usage)**

- Max **5** attempts.
- Respect **Retry-After**-header.
- Liner backoff with 1s (initial value) + 2s (slope).
- Triggers on 4xx, 5xx and `Psr\Http\Client\ClientExceptionInterface`.
- Log on any error (unacceptable response or `Psr\Http\Client\ClientExceptionInterface`-exception).

```shell
<?php

use Mingalevme\RetryablePsrHttpClient\BackoffCalc\LinearBackoffCalc;
use Mingalevme\RetryablePsrHttpClient\Config;
use Mingalevme\RetryablePsrHttpClient\NullEventListener;
use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\ResponseAnalyzerInterface;
use Mingalevme\RetryablePsrHttpClient\RetryablePsrHttpClient;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class MyAppHttpClientErrLogEventListener extends AbstractEventListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function onError(
        int $attemptNumber,
        RequestInterface $request,
        ClientExceptionInterface|ResponseInterface $error,
    ): void {
        $this->logger->error("Error while sending request {$request->getUri()}, attempt #$attemptNumber");
    }
}

final class MyAppHttpResponseAnalyzer implements ResponseAnalyzerInterface
{
    public function isAcceptable(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 400;
    }
}

$someDiContainer->decorate(
    ClientInterface::class,
    function (
        ClientInterface $client,
        MyAppHttpClientErrLogEventListener $listener,
        MyAppHttpResponseAnalyzer $responseAnalyzer,
    ): RetryablePsrHttpClient {
        $config = Config::new()
            ->setRetryCount(5)
            ->setRespectRetryAfterHeader(true)
            ->setBackoffCalc(new LinearBackoffCalc(2, 1))
            ->setResponseAnalyzer($responseAnalyzer)
            ->addEventListener($listener);
        return new RetryablePsrHttpClient($client, $config);
    },
);
```
