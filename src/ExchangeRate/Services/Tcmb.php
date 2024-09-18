<?php

namespace Yavuz\ExchangeRates\ExchangeRate\Services;

use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yavuz\ExchangeRates\ExchangeRate\ExchangeRateInterface;
use Yavuz\ExchangeRates\ExchangeRate\Service;

class Tcmb extends Service implements ExchangeRateInterface
{
    /**
     * @var string
     */
    private string $API_URL = 'https://www.tcmb.gov.tr/kurlar/today.xml';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->isLife($this->API_URL);
    }

    /**
     * @param string $URL
     * @return void
     */
    public function isLife(string $URL): void
    {
        try {
            (new Client())->request('GET', $URL);
        } catch (GuzzleException $e) {
            dump($e->getMessage());
            exit;
        }
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        try {
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
     * @return array|null
     */
    public function getRemoteResult(): ?array
    {
        $this->validate();

        $result = match ($this->baseCurrency) {
            'TRY' => $this->getRemoteResultForTRY(),
            'default' => $this->getRemoteResultForNonTRY()
        };

        if (!empty($result) && $this->cacheTime > 0) {
            $this->cache->writeCache(serialize($result));
        }

        return $result;
    }

    /**
     * @return array|null
     */
    public function getRemoteResultForTRY(): ?array
    {
        try {
            $xml = new DOMDocument();
            $xml->load($this->API_URL);
            $currencies = $xml->getElementsByTagName('Currency');

            $result = [];

            if (is_string($this->currencies)) {
                foreach ($currencies as $currency) {
                    $code = $currency->getAttribute('CurrencyCode');

                    if ($code == $this->currencies) {
                        $result[] = [
                            'code' => $code,
                            'currencyName' => $currency->getElementsByTagName('Isim')->item(0)->nodeValue,
                            'forexBuying' => number_format($currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue, parent::DECIMAL),
                            'forexSelling' => number_format($currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue, parent::DECIMAL),
                            'banknoteBuying' => number_format($currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue, parent::DECIMAL),
                            'banknoteSelling' => number_format($currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue, parent::DECIMAL)
                        ];
                        break;
                    }
                }
            } else if (is_array($this->currencies)) {
                foreach ($this->currencies as $currencyCode) {
                    foreach ($currencies as $currency) {
                        $code = $currency->getAttribute('CurrencyCode');

                        if ($code == $currencyCode) {
                            $result[] = [
                                'code' => $code,
                                'currencyName' => $currency->getElementsByTagName('Isim')->item(0)->nodeValue,
                                'forexBuying' => number_format($currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue, parent::DECIMAL),
                                'forexSelling' => number_format($currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue, parent::DECIMAL),
                                'banknoteBuying' => number_format($currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue, parent::DECIMAL),
                                'banknoteSelling' => number_format($currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue, parent::DECIMAL)
                            ];
                        }
                    }
                }
            }

            return $result;
        } catch (Exception $e) {
            dump($e->getMessage());
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getRemoteResultForNonTRY(): ?array
    {
        try {
            $xml = new DOMDocument();
            $xml->load($this->API_URL);
            $currencies = $xml->getElementsByTagName('Currency');

            $result = [];

            if (is_string($this->currencies)) {
                $baseCurForexBuyingVal = 0;
                $baseCurForexSellingVal = 0;
                $baseCurBanknoteBuyingVal = 0;
                $baseCurBanknoteSellingVal = 0;

                foreach ($currencies as $currency) {
                    if ($this->baseCurrency === $currency->getAttribute('CurrencyCode')) {
                        $baseCurForexBuyingVal = $currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue;
                        $baseCurForexSellingVal = $currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue;
                        $baseCurBanknoteBuyingVal = $currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue;
                        $baseCurBanknoteSellingVal = $currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue;
                        break;
                    }
                }

                if ($baseCurForexBuyingVal === 0) {
                    throw new Exception('Invalid base currency!');
                }

                foreach ($currencies as $currency) {
                    $code = $currency->getAttribute('CurrencyCode');

                    if ($code == $this->currencies) {
                        $result[] = [
                            'code' => $code,
                            'currencyName' => $currency->getElementsByTagName('Isim')->item(0)->nodeValue,
                            'forexBuying' => number_format(1 / $currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue * $baseCurForexBuyingVal, parent::DECIMAL),
                            'forexSelling' => number_format(1 / $currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue * $baseCurForexSellingVal, parent::DECIMAL),
                            'banknoteBuying' => number_format(1 / $currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue * $baseCurBanknoteBuyingVal, parent::DECIMAL),
                            'banknoteSelling' => number_format(1 / $currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue * $baseCurBanknoteSellingVal, parent::DECIMAL)
                        ];
                        break;
                    }
                }

                if ($this->baseCurrency === 'TRY') {
                    $result[] = [
                        'code' => 'TRY',
                        'currencyName' => 'TÜRK LİRASI',
                        'forexBuying' => number_format($baseCurForexBuyingVal, parent::DECIMAL),
                        'forexSelling' => number_format($baseCurForexSellingVal, parent::DECIMAL),
                        'banknoteBuying' => number_format($baseCurBanknoteBuyingVal, parent::DECIMAL),
                        'banknoteSelling' => number_format($baseCurBanknoteSellingVal, parent::DECIMAL)
                    ];
                }
            } else if (is_array($this->currencies)) {
                $baseCurForexBuyingVal = 0;
                $baseCurForexSellingVal = 0;
                $baseCurBanknoteBuyingVal = 0;
                $baseCurBanknoteSellingVal = 0;

                foreach ($currencies as $currency) {
                    if ($this->baseCurrency === $currency->getAttribute('CurrencyCode')) {
                        $baseCurForexBuyingVal = $currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue;
                        $baseCurForexSellingVal = $currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue;
                        $baseCurBanknoteBuyingVal = $currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue;
                        $baseCurBanknoteSellingVal = $currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue;
                        break;
                    }
                }

                if ($baseCurForexBuyingVal === 0) {
                    throw new Exception('Invalid base currency!');
                }

                foreach ($this->currencies as $currencyCode) {
                    foreach ($currencies as $currency) {
                        $code = $currency->getAttribute('CurrencyCode');

                        if ($code === $currencyCode) {
                            $result[] = [
                                'code' => $code,
                                'currencyName' => $currency->getElementsByTagName('Isim')->item(0)->nodeValue,
                                'forexBuying' => number_format(1 / $currency->getElementsByTagName('ForexBuying')->item(0)->nodeValue * $baseCurForexBuyingVal, parent::DECIMAL),
                                'forexSelling' => number_format(1 / $currency->getElementsByTagName('ForexSelling')->item(0)->nodeValue * $baseCurForexSellingVal, parent::DECIMAL),
                                'banknoteBuying' => number_format(1 / $currency->getElementsByTagName('BanknoteBuying')->item(0)->nodeValue * $baseCurBanknoteBuyingVal, parent::DECIMAL),
                                'banknoteSelling' => number_format(1 / $currency->getElementsByTagName('BanknoteSelling')->item(0)->nodeValue * $baseCurBanknoteSellingVal, parent::DECIMAL)
                            ];
                        }
                    }

                    if ($this->baseCurrency === 'TRY') {
                        $result[] = [
                            'code' => 'TRY',
                            'currencyName' => 'TÜRK LİRASI',
                            'forexBuying' => number_format($baseCurForexBuyingVal, parent::DECIMAL),
                            'forexSelling' => number_format($baseCurForexSellingVal, parent::DECIMAL),
                            'banknoteBuying' => number_format($baseCurBanknoteBuyingVal, parent::DECIMAL),
                            'banknoteSelling' => number_format($baseCurBanknoteSellingVal, parent::DECIMAL)
                        ];
                    }
                }
            }

            return $result;
        } catch (Exception $e) {
            dump($e->getMessage());
        }

        return null;
    }
}
