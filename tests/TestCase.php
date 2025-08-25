<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient;

use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private ?HttpFactory $guzzleHttpFactory = null;

    private function getGuzzleHttpFactory(): HttpFactory
    {
        if (!$this->guzzleHttpFactory) {
            $this->guzzleHttpFactory = new HttpFactory();
        }
        return $this->guzzleHttpFactory;
    }

    protected function createRequest(string $method, UriInterface|string $uri): RequestInterface
    {
        return $this->getGuzzleHttpFactory()->createRequest($method, $uri);
    }

    protected function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->getGuzzleHttpFactory()->createResponse($code, $reasonPhrase);
    }

    protected function createStream(string $content = ''): StreamInterface
    {
        return $this->getGuzzleHttpFactory()->createStream($content);
    }
}
