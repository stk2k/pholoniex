<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';

use Pholoniex\PholoniexClient;

$client = new PholoniexClient();

try{
// call web API
    $volume_24h = $client->getVolume24h();

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'volume_24h:' . print_r($volume_24h, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
