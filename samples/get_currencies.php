<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';

use Pholoniex\PholoniexClient;

$client = new PholoniexClient();

try{
    $start = time() - 86400;    // a day before
    $end = 9999999999;
    
// call web API
    $currencies = $client->getCurrencies();

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'currencies:' . print_r($currencies, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
