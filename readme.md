# PHP Currency Library

[![Latest Stable Version](https://img.shields.io/badge/packagist-v1.0.0-blue
)](https://packagist.org/packages/yavuz/exchange-rates)

Exchange rates API built using Dependency Injection.

This library requires PHP >= 8.1

## Installation

The recommended way to install the IMAP library is through [Composer](https://getcomposer.org):

```bash
$ composer require yavuz/exchange-rates
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Usage

### Usage Example TCMB Driver

```php

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

```

### Usage Example CurrencyAPI Driver

```php

use Yavuz\ExchangeRates\ExchangeRate\ExchangeRate;
use Yavuz\ExchangeRates\ExchangeRate\Services\CurrencyAPI;

require_once 'vendor/autoload.php';
require_once 'src/Config/Config.php';
require_once 'src/Helper/Helper.php';

try {
    $exchangeRatesCurrencyAPI = (new ExchangeRate(new CurrencyAPI()))
        ->setAPIKey('api-key')
        ->setBaseCurrency('TRY')
        ->setCurrencies('GBP')
        ->getResult();

    dump($exchangeRatesCurrencyAPI);
} catch (Exception $e) {
    echo $e->getMessage();
}

```

## License

Faker is released under the MIT License. See [`LICENSE`](https://github.com/yavuzahmet89/exchange-rates/blob/main/LICENSE) for details.