<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit;

use Mingalevme\RetryablePsrHttpClient\NullEventListener;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

final class NullEventListenerTest extends TestCase
{
    public function test(): void
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->createResponse();
        $exception = new class extends RuntimeException implements ClientExceptionInterface {
        };
        $listener = new class extends NullEventListener {
        };
        $listener->onRequest(1, $request);
        $listener->onException(1, $request, $exception);
        $listener->onResponse(1, $request, $response);
        $listener->onErrorResponse(1, $request, $response);
        $listener->onError(1, $request, $response);
        $listener->onError(1, $request, $exception);
        self::assertTrue(true);
    }
}
