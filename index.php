<?php

use Yavuz\ExchangeRates\ExchangeRate\ExchangeRate;
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
} catch (Exception $e) {
    echo $e->getMessage();
}
