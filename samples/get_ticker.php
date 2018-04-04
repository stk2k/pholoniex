<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';

use Pholoniex\PholoniexClient;

$client = new PholoniexClient();

try{
// call web API
    $ticker = $client->getTicker();

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'ticker:' . print_r($ticker, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
