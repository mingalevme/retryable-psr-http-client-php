<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit;

use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\Http5xx429ResponseAnalyzer;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;

/**
 * @see Http5xx429ResponseAnalyzer
 */
final class Http5xx429ResponseAnalyzerTest extends TestCase
{
    public function test(): void
    {
        $analyzer = new Http5xx429ResponseAnalyzer();
        self::assertSame(true, $analyzer->isAcceptable($this->createResponse(100)));
        self::assertSame(true, $analyzer->isAcceptable($this->createResponse(200)));
        self::assertSame(true, $analyzer->isAcceptable($this->createResponse(300)));
        self::assertSame(true, $analyzer->isAcceptable($this->createResponse(400)));
        self::assertSame(false, $analyzer->isAcceptable($this->createResponse(429)));
        self::assertSame(false, $analyzer->isAcceptable($this->createResponse(500)));
        self::assertSame(false, $analyzer->isAcceptable($this->createResponse(501)));
    }
}
