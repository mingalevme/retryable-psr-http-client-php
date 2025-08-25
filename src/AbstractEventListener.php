<?php

declare(strict_types=1);

namespace Mingalevme\RetryablePsrHttpClient;

abstract class AbstractEventListener implements EventListenerInterface
{
    use AbstractEventListenerTrait;
}
