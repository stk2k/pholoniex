<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

$argdefs = array(
    '[currency_pair]' => 'string',
    '[depth]' => 'integer',
);
list($currency_pair, $depth) = get_args($argdefs,__FILE__);

$client = new PholoniexClient();

try{
// call web API
    $order_book = $client->getOrderBook($currency_pair, $depth);

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'order_book:' . print_r($order_book, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
