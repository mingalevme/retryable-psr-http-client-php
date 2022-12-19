<?php

declare(strict_types=1);

namespace Mingalevme\Tests\RetryablePsrHttpClient\Suites\Unit;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Mingalevme\PsrHttpClientStubs\HistoryPsrHttpClientDecorator;
use Mingalevme\PsrHttpClientStubs\QueuePsrHttpClient;
use Mingalevme\PsrHttpClientStubs\StaticResponseMapPsrHttpClient;
use Mingalevme\RetryablePsrHttpClient\BackoffCalc\ExponentialBackoffCalc;
use Mingalevme\RetryablePsrHttpClient\Config;
use Mingalevme\RetryablePsrHttpClient\RetryablePsrHttpClient;
use Mingalevme\Tests\RetryablePsrHttpClient\TestCase;
use Mingalevme\Tests\RetryablePsrHttpClient\Utils\ArrayEventListener;
use Mingalevme\Tests\RetryablePsrHttpClient\Utils\ArraySleeper;
use Mingalevme\Tests\RetryablePsrHttpClient\Utils\ListOfValuesClock;
use Mingalevme\Tests\RetryablePsrHttpClient\Utils\NoRedirectResponseAnalyzer;
use Mingalevme\Tests\RetryablePsrHttpClient\Utils\RequestBodyReaderPsrHttpClientDecorator;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * @see RetryablePsrHttpClient
 */
final class RetryablePsrHttpClientTest extends TestCase
{
    /**
     * @throws ClientExceptionInterface
     */
    public function testRetrying(): void
    {
        $retryCount = 5;
        $request200 = $this->createRequest('GET', '/test-200');
        $response200 = $this->createResponse();
        $request301 = $this->createRequest('GET', '/test-301');
        $response301 = $this->createResponse(301);
        $requestExc = $this->createRequest('GET', '/test-exception');
        $responseExc = new class extends RuntimeException implements ClientExceptionInterface {
        };
        $psrHttpClient = (new StaticResponseMapPsrHttpClient())
            ->add('GET', '/test-200', $response200)
            ->add('GET', '/test-301', $response301)
            ->add('GET', '/test-exception', $responseExc);
        $historyPsrHttpClient = new HistoryPsrHttpClientDecorator($psrHttpClient);
        $sleeper = new ArraySleeper();
        $eventListener = new ArrayEventListener();
        $config = Config::new()
            ->setRetryCount($retryCount)
            ->setSleeper($sleeper)
            ->setBackoffCalc(new ExponentialBackoffCalc())
            ->setResponseAnalyzer(new NoRedirectResponseAnalyzer())
            ->setEventListeners(null) // code-cov
            ->addEventListener($eventListener);
        $retryableDmpHttpClient = new RetryablePsrHttpClient($historyPsrHttpClient, $config);

        // 200

        $historyPsrHttpClient->clear();
        $sleeper->clear();
        $eventListener->clear();
        // 200 / History
        self::assertSame($response200, $retryableDmpHttpClient->sendRequest($request200));
        self::assertCount(1, $historyPsrHttpClient->getHistory());
        self::assertSame($request200, $historyPsrHttpClient->getHistory()[0]->getRequest());
        self::assertSame($response200, $historyPsrHttpClient->getHistory()[0]->getResult());
        self::assertSame($response200, $historyPsrHttpClient->getHistory()[0]->getResponse());
        // 200 / Sleeper
        self::assertCount(0, $sleeper->getSleeps());
        // 200 / EventListener
        self::assertCount(1, $eventListener->getOnRequest());
        self::assertSame([1, $request200], $eventListener->getOnRequest()[0]);
        self::assertCount(0, $eventListener->getOnException());
        self::assertCount(1, $eventListener->getOnResponse());
        self::assertSame([1, $request200, $response200], $eventListener->getOnResponse()[0]);
        self::assertCount(0, $eventListener->getOnErrorResponse());
        self::assertCount(0, $eventListener->getOnError());

        // 301

        $historyPsrHttpClient->clear();
        $sleeper->clear();
        $eventListener->clear();
        // 301 / History
        self::assertSame($response301, $retryableDmpHttpClient->sendRequest($request301));
        self::assertCount($retryCount, $historyPsrHttpClient->getHistory());
        foreach ($historyPsrHttpClient->getHistory() as $item) {
            self::assertSame($request301, $item->getRequest());
            self::assertSame($response301, $item->getResult());
            self::assertSame($response301, $item->getResponse());
            self::assertSame(null, $item->getException());
        }
        // 301 / Sleeper
        self::assertCount($retryCount - 1, $sleeper->getSleeps());
        self::assertSame([1, 2, 4, 8], $sleeper->getSleeps());
        // 301 / EventListener
        self::assertCount($retryCount, $eventListener->getOnRequest());
        self::assertCount(0, $eventListener->getOnException());
        self::assertCount($retryCount, $eventListener->getOnResponse());
        self::assertCount($retryCount, $eventListener->getOnErrorResponse());
        self::assertCount($retryCount, $eventListener->getOnError());
        foreach (range(0, $retryCount - 1) as $i) {
            self::assertSame([$i + 1, $request301], $eventListener->getOnRequest()[$i]);
            self::assertSame([$i + 1, $request301, $response301], $eventListener->getOnResponse()[$i]);
            self::assertSame([$i + 1, $request301, $response301], $eventListener->getOnErrorResponse()[$i]);
            self::assertSame([$i + 1, $request301, $response301], $eventListener->getOnError()[$i]);
        }

        // Exception

        $historyPsrHttpClient->clear();
        $sleeper->clear();
        $eventListener->clear();
        // Exception / History
        try {
            self::assertSame($responseExc, $retryableDmpHttpClient->sendRequest($requestExc));
        } catch (ClientExceptionInterface $e) {
            self::assertSame($responseExc, $e);
        }
        self::assertCount($retryCount, $historyPsrHttpClient->getHistory());
        foreach ($historyPsrHttpClient->getHistory() as $item) {
            self::assertSame($requestExc, $item->getRequest());
            self::assertSame($responseExc, $item->getResult());
            self::assertSame(null, $item->getResponse());
            self::assertSame($responseExc, $item->getException());
        }
        // Exception / Sleeper
        self::assertCount($retryCount - 1, $sleeper->getSleeps());
        self::assertSame([1, 2, 4, 8], $sleeper->getSleeps());
        // Exception / EventListener
        self::assertCount($retryCount, $eventListener->getOnRequest());
        self::assertCount($retryCount, $eventListener->getOnException());
        self::assertCount(0, $eventListener->getOnResponse());
        self::assertCount(0, $eventListener->getOnErrorResponse());
        self::assertCount($retryCount, $eventListener->getOnError());
        foreach (range(0, $retryCount - 1) as $i) {
            self::assertSame([$i + 1, $requestExc], $eventListener->getOnRequest()[$i]);
            self::assertSame([$i + 1, $requestExc, $responseExc], $eventListener->getOnException()[$i]);
            self::assertSame([$i + 1, $requestExc, $responseExc], $eventListener->getOnError()[$i]);
        }
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function testPostBodyRewind(): void
    {
        $retryCount = 3;
        $request = $this->createRequest('POST', '/test')
            ->withBody($this->createStream('test'));
        $response = $this->createResponse(500);
        $psrHttpClient = (new StaticResponseMapPsrHttpClient())
            ->add('POST', '/test', $response);
        $psrHttpClient = new RequestBodyReaderPsrHttpClientDecorator($psrHttpClient);
        $sleeper = new ArraySleeper();
        $config = Config::new()
            ->setRetryCount($retryCount)
            ->setSleeper($sleeper);
        $retryableDmpHttpClient = new RetryablePsrHttpClient($psrHttpClient, $config);
        self::assertSame($response, $retryableDmpHttpClient->sendRequest($request));
        self::assertSame(array_fill(0, $retryCount, 'test'), $psrHttpClient->getBodies());
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function testRetryAfterSecondsHeader(): void
    {
        $retryCount = 3;
        $request = $this->createRequest('GET', '/test');
        $response1 = $this->createResponse(500)
            ->withHeader('Retry-After', '10');
        $response2 = $this->createResponse(500)
            ->withHeader('Retry-After', '20');
        $response3 = $this->createResponse(500)
            ->withHeader('Retry-After', '30');
        $psrHttpClient = (new QueuePsrHttpClient())
            ->push($response1)
            ->push($response2)
            ->push($response3);
        $sleeper = new ArraySleeper();
        $config = Config::new()
            ->setRetryCount($retryCount)
            ->setSleeper($sleeper);
        $retryableDmpHttpClient = new RetryablePsrHttpClient($psrHttpClient, $config);
        self::assertSame($response3, $retryableDmpHttpClient->sendRequest($request));
        self::assertSame([10, 20], $sleeper->getSleeps());
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function testRetryAfterDateHeader(): void
    {
        $retryCount = 3;
        /** @var DateTimeImmutable $now */
        $now = DateTimeImmutable::createFromFormat('U', (string)time());
        $nowPlus10s = $now->add(DateInterval::createFromDateString('10 seconds'));
        $nowPlus15s = $now->add(DateInterval::createFromDateString('15 seconds'));
        $request = $this->createRequest('GET', '/test');
        $response1 = $this->createResponse(500)
            ->withHeader('Retry-After', $nowPlus10s->format(DateTimeInterface::RFC1123));
        $response2 = $this->createResponse(500)
            ->withHeader('Retry-After', $nowPlus15s->format(DateTimeInterface::RFC1123));
        $response3 = $this->createResponse(500);
        $psrHttpClient = (new QueuePsrHttpClient())
            ->push($response1)
            ->push($response2)
            ->push($response3);
        $clock = new ListOfValuesClock([$now, $nowPlus10s]);
        $sleeper = new ArraySleeper();
        $config = Config::new()
            ->setRespectRetryAfterHeader(true)
            ->setRetryCount($retryCount)
            ->setSleeper($sleeper)
            ->setClock($clock);
        $retryableDmpHttpClient = new RetryablePsrHttpClient($psrHttpClient, $config);
        self::assertSame($response3, $retryableDmpHttpClient->sendRequest($request));
        self::assertSame([10.0, 5.0], $sleeper->getSleeps());
    }
}
