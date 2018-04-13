<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

list($api_key, $api_secret) = poloniex_credentials();

$client = new PholoniexClient($api_key, $api_secret);

try{
// call web API
    $result = $client->sell('BTC_ETH', 0.05925626, 0.001);

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'result:' . print_r($result, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
