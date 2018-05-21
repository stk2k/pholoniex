<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

list($api_key, $api_secret) = poloniex_credentials();

$client = new PholoniexClient($api_key, $api_secret);

$sell_amount = 0.002;

try{
// call web API
    $ticker = $client->getTicker();
    $rate = isset($ticker['BTC_ETH']) ? $ticker['BTC_ETH'] : 0;
    if ($rate){
        $rate = $rate['last'];
        $result = $client->sell('BTC_ETH', $rate, $sell_amount);
        echo 'result:' . print_r($result, true) . PHP_EOL;
    }

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
