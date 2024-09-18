<?php

namespace Yavuz\ExchangeRates\ExchangeRate;

class ExchangeRate
{
    /**
     * @var ExchangeRateInterface
     */
    private ExchangeRateInterface $service;

    /**
     * @param ExchangeRateInterface $exchangeRate
     */
    public function __construct(ExchangeRateInterface $exchangeRate)
    {
        $this->service = $exchangeRate;
    }

    /**
     * @param string $APIKey
     * @return $this
     */
    public function setAPIKey(string $APIKey): self
    {
        $this->service->setAPIKey($APIKey);
        return $this;
    }

    /**
     * @param string $baseCurrency
     * @return $this
     */
    public function setBaseCurrency(string $baseCurrency): self
    {
        $this->service->setBaseCurrency($baseCurrency);
        return $this;
    }

    /**
     * @param string|array $currencies
     * @return $this
     */
    public function setCurrencies(string|array $currencies): self
    {
        $this->service->setCurrencies($currencies);
        return $this;
    }

    /**
     * @param int $cacheTime
     * @return $this
     */
    public function setCacheTime(int $cacheTime): self
    {
        $this->service->setCacheTime($cacheTime);
        return $this;
    }

    /**
     * @return array|null
     */
    public function getResult(): ?array
    {
        return $this->service->getResult();
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        $this->service->clearCache();
    }
}
