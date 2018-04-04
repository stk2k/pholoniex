<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

list($api_key, $api_secret) = poloniex_credentials();

$client = new PholoniexClient($api_key, $api_secret);

try{
// call web API
    $balances = $client->getBalances();

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'balances:' . print_r($balances, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
