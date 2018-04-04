<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

$argdefs = array(
    'currency_pair' => 'string',
    '[period]' => 'integer',
);
list($currency_pair, $period) = get_args($argdefs,__FILE__);

$client = new PholoniexClient();

try{
    $start = time() - 86400;    // a day before
    $end = 9999999999;
    
// call web API
    $chart_data = $client->getChartData($currency_pair, $start, $end, $period);

// show request URI
    $uri = $client->getLastRequest()->getUrl();
    echo 'URI:' . PHP_EOL;
    echo ' ' . $uri . PHP_EOL;

    echo 'chart_data:' . print_r($chart_data, true) . PHP_EOL;
}
catch(\Exception $e)
{
    print_stacktrace($e);
}
