<?php

use Yavuz\ExchangeRates\ExchangeRate\ExchangeRate;
use Yavuz\ExchangeRates\ExchangeRate\Services\CurrencyAPI;
use Yavuz\ExchangeRates\ExchangeRate\Services\Tcmb;

require_once 'vendor/autoload.php';
require_once 'src/Config/Config.php';
require_once 'src/Helper/Helper.php';

try {
    $exchangeRatesTCMB = (new ExchangeRate(new Tcmb()))
        ->setBaseCurrency('TRY')
        ->setCurrencies(['USD', 'EUR'])
        ->setCacheTime(1800)
        ->getResult();

    dump($exchangeRatesTCMB);

    $exchangeRatesCurrencyAPI = (new ExchangeRate(new CurrencyAPI()))
        ->setAPIKey('api-key')
        ->setBaseCurrency('TRY')
        ->setCurrencies('GBP')
        ->getResult();

    dump($exchangeRatesCurrencyAPI);
} catch (Exception $e) {
    echo $e->getMessage();
}
