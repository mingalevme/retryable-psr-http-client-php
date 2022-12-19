<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Utils;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RequestBodyReaderPsrHttpClientDecorator implements ClientInterface
{
    private ClientInterface $client;
    /** @var string[] */
    private array $bodies = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->bodies[] = $request->getBody()->getContents();
        return $this->client->sendRequest($request);
    }

    /**
     * @return string[]
     */
    public function getBodies(): array
    {
        return $this->bodies;
    }
}
