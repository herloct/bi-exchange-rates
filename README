# BI Exchange Rates

[![Build Status](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/build-status/develop) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/?branch=develop) [![Code Coverage](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/herloct/bi-exchange-rates/?branch=develop)

Bank Indonesia exchange rates wrapper for PHP.

Basic usage:
```php
use \Kuartet\BI\ExchangeRate;
use \Kuartet\BI\Fetcher\Exception\ConnectionException;
use \Kuartet\BI\Parser\Exception\ParseException;

try {
	$exchangeRate = new ExchangeRate();
	$rates = $exchangeRate->getUpdates();
	var_dump($rates);
} catch (ConnectionException $ex) {
    // Do something for connection error, http code 5xx, 4xx
} catch (ParseException $ex) {
	// Do something for parsing error
}
```
