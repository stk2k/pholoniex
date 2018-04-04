<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

$argdefs = array(
    'currency_pair' => 'string',
    '[start]' => 'integer',
    '[end]' => 'integer',
);
list($currency_pair, $start, $end) = get_args($argdefs,__FILE__);

$client = new PholoniexClient();

try{
// call web API
    $trade_history = $client->getTradeHistory($currency_pair, $start, $end);

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'trade_history:' . print_r($trade_history, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
