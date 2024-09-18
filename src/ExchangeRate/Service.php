<?php

namespace Yavuz\ExchangeRates\ExchangeRate;

use Yavuz\ExchangeRates\ExchangeRate\Cache\Cache;

abstract class Service
{
    /**
     * @var string
     */
    protected string $APIKey;

    /**
     * @var string
     */
    protected string $baseCurrency;

    /**
     * @var string|array
     */
    protected string|array $currencies;

    /**
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @var int
     */
    protected int $cacheTime = 3600;

    /**
     * @var string
     */
    protected string $cacheFile;

    /**
     * @var int
     */
    const DECIMAL = 4;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cache = (new Cache())->setCacheFile(get_short_class_name(get_class($this)));
    }

    /**
     * @param string $APIKey
     * @return $this
     */
    public function setAPIKey(string $APIKey): self
    {
        $this->APIKey = $APIKey;
        return $this;
    }

    /**
     * @param string $baseCurrency
     * @return $this
     */
    public function setBaseCurrency(string $baseCurrency): self
    {
        $this->baseCurrency = strtoupper($baseCurrency);
        return $this;
    }

    /**
     * @param string|array $currency
     * @return $this
     */
    public function setCurrencies(string|array $currency): self
    {
        if (is_string($currency)) {
            $this->currencies = strtoupper($currency);
        } else if (is_array($currency)) {
            $this->currencies = array_map('strtoupper', $currency);
        }

        return $this;
    }

    /**
     * @return string|array
     */
    public function getCurrencies(): string|array
    {
        return $this->currencies;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setCacheTime(int $seconds): self
    {
        $this->cacheTime = $seconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    /**
     * @return array|bool|null
     */
    public function getCacheResult(): array|bool|null
    {
        return unserialize($this->cache->readCache());
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        $this->cache->clearCache();
    }

    /**
     * @return array|null
     */
    public function getResult(): ?array
    {
        if ($this->cacheTime === 0 || !file_exists($this->cache->getCacheFile())) {
            return $this->getRemoteResult();
        }

        if (time() - $this->cacheTime > filemtime($this->cache->getCacheFile()) || !$this->getCacheResult()) {
            return $this->getRemoteResult();
        }

        $cacheResult = $this->getCacheResult();
        $currencies = [];

        foreach ($cacheResult as $value) {
            if (isset($value['code'])) {
                $currencies[] = $value['code'];
            }
        }

        if (array_diff($currencies, (array)$this->currencies) !== []) {
            return $this->getRemoteResult();
        }

        return $cacheResult;
    }
}
