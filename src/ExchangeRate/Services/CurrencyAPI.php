<?php

namespace Yavuz\ExchangeRates\ExchangeRate\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yavuz\ExchangeRates\ExchangeRate\ExchangeRateInterface;
use Yavuz\ExchangeRates\ExchangeRate\Service;

class CurrencyAPI extends Service implements ExchangeRateInterface
{
    /**
     * @var string
     */
    private string $API_URL = 'https://api.currencyapi.com/v3/latest?base_currency={baseCurrency}&currencies={currencies}&apikey={APIKey}';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        try {
            if (empty($this->APIKey)) {
                throw new Exception('API Key not found!');
            }

            if (empty($this->baseCurrency)) {
                throw new Exception('Base currency not found!');
            }

            if (empty($this->currencies)) {
                throw new Exception('Currencies not found!');
            }
        } catch (Exception $e) {
            dump($e->getMessage());
            exit;
        }
    }

    /**
     * @return string
     */
    private function getAPIURL(): string
    {
        $search = [
            '{baseCurrency}',
            '{currencies}',
            '{APIKey}'
        ];

        $replace = [
            $this->baseCurrency,
            is_array($this->currencies) ? implode(',', $this->currencies) : (string)$this->currencies,
            $this->APIKey
        ];

        return str_replace(
            $search,
            $replace,
            $this->API_URL
        );
    }

    /**
     * @return array|null
     */
    public function getRemoteResult(): ?array
    {
        try {
            $this->validate();

            $_result = (new Client())
                ->request('GET', $this->getAPIURL())
                ->getBody()
                ->getContents();

            $result = json_decode($_result, true);

            if (!empty($result) && $this->cacheTime > 0) {
                $this->cache->writeCache(serialize($result));
            }

            return $result;
        } catch (GuzzleException $e) {
            dump($e->getMessage());
        } catch (Exception $e) {
            dump($e->getMessage());
        }

        return null;
    }
}
