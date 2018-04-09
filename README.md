pholoniex, poloniex API PHP client
=======================

## Description

pholoniex is a PHP library which provides calling poloniex-API.

## Feature

- simple interface

## Demo

### simple and fastest sample:
```php
use Pholoniex\PholoniexClient;
 
$client = new PholoniexClient();
    
$ticker_list = $client->getTicker();
    
foreach($ticker_list as $symbol => $ticker){
    echo "[$symbol]" . $ticker['last'] . PHP_EOL;
}
 
```

## Usage

1. create PholoniexClient object.
2. call API method.
3. The method returns array or object(stdClass).

## Requirement

PHP 5.5 or later


## Installing pholoniex

The recommended way to install pholoniex is through
[Composer](http://getcomposer.org).

```bash
composer require stk2k/pholoniex
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## License
[MIT](https://github.com/stk2k/pholoniex/blob/master/LICENSE)

## Author

[stk2k](https://github.com/stk2k)

## Disclaimer

This software is no warranty.

We are not responsible for any results caused by the use of this software.

Please use the responsibility of the your self.


## Donation

-Bitcoin: 3HCw9pp6dSq1xU9iPoPKVFyVbM8iBrrinn
