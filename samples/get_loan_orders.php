<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

$argdefs = array(
    'currency' => 'string',
);
list($currency) = get_args($argdefs,__FILE__);

$client = new PholoniexClient();

try{
    $start = time() - 86400;    // a day before
    $end = 9999999999;
    
// call web API
    $loan_orders = $client->getLoanOrders($currency);

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'loan_orders:' . print_r($loan_orders, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
