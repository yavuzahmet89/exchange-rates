<?php

namespace Yavuz\ExchangeRates\ExchangeRate;

interface ExchangeRateInterface
{
    /**
     * @return array|null
     */
    public function getRemoteResult(): ?array;
}
