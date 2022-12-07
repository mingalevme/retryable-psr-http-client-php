<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

use Mingalevme\RetryablePsrHttpClient\BackoffCalc\BackoffCalcInterface;
use Mingalevme\RetryablePsrHttpClient\ResponseAnalyzer\ResponseAnalyzerInterface;
use Mingalevme\RetryablePsrHttpClient\Sleeper\SleeperInterface;
use Psr\Clock\ClockInterface;

final class Config
{
    /** @var int<1, max>|null */
    private ?int $retryCount = null;
    private ?BackoffCalcInterface $backoffCalc = null;
    private ?SleeperInterface $sleeper = null;
    private ?ResponseAnalyzerInterface $responseAnalyzer = null;
    /** @var list<EventListenerInterface> */
    private ?array $eventListeners = [];
    private ?bool $respectRetryAfterHeader = null;
    private ?ClockInterface $clock = null;

    public static function new(): self
    {
        return new self();
    }

    private function __construct()
    {
    }

    /**
     * @param int<1, max> $value
     * @return $this
     */
    public function setRetryCount(int $value): self
    {
        $this->retryCount = $value;
        return $this;
    }

    /**
     * @return int<1, max>|null
     */
    public function getRetryCount(): ?int
    {
        return $this->retryCount;
    }

    /**
     * @param BackoffCalcInterface|null $value
     * @return $this
     */
    public function setBackoffCalc(?BackoffCalcInterface $value): self
    {
        $this->backoffCalc = $value;
        return $this;
    }

    public function getBackoffCalc(): ?BackoffCalcInterface
    {
        return $this->backoffCalc;
    }

    /**
     * @param SleeperInterface|null $value
     * @return $this
     */
    public function setSleeper(?SleeperInterface $value): self
    {
        $this->sleeper = $value;
        return $this;
    }

    public function getSleeper(): ?SleeperInterface
    {
        return $this->sleeper;
    }

    /**
     * @param ResponseAnalyzerInterface|null $responseAnalyzer
     * @return $this
     */
    public function setResponseAnalyzer(?ResponseAnalyzerInterface $responseAnalyzer): self
    {
        $this->responseAnalyzer = $responseAnalyzer;
        return $this;
    }

    public function getResponseAnalyzer(): ?ResponseAnalyzerInterface
    {
        return $this->responseAnalyzer;
    }

    /**
     * @param list<EventListenerInterface>|null $value
     * @return $this
     */
    public function setEventListeners(?array $value): self
    {
        $this->eventListeners = (array)$value;
        return $this;
    }

    /**
     * @param EventListenerInterface $value
     * @return $this
     */
    public function addEventListener(EventListenerInterface $value): self
    {
        $this->eventListeners[] = $value;
        return $this;
    }

    /**
     * @return list<EventListenerInterface>|null
     */
    public function getEventListeners(): ?array
    {
        return $this->eventListeners ?: null;
    }

    /**
     * @param bool|null $respectRetryAfterHeader
     * @return $this
     */
    public function setRespectRetryAfterHeader(?bool $respectRetryAfterHeader): Config
    {
        $this->respectRetryAfterHeader = $respectRetryAfterHeader;
        return $this;
    }

    public function getRespectRetryAfterHeader(): ?bool
    {
        return $this->respectRetryAfterHeader;
    }

    /**
     * @param ClockInterface|null $clock
     * @return $this
     */
    public function setClock(?ClockInterface $clock): Config
    {
        $this->clock = $clock;
        return $this;
    }

    public function getClock(): ?ClockInterface
    {
        return $this->clock;
    }
}
