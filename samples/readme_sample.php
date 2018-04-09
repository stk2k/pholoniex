<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/include/autoload.php';
require dirname(__FILE__) . '/include/sample.inc.php';

use Pholoniex\PholoniexClient;

echo '===[ simple and fastest sample ]===', PHP_EOL;

try{
    $client = new PholoniexClient();
    
    $ticker_list = $client->getTicker();
    
    foreach($ticker_list as $symbol => $ticker){
        echo "[$symbol]" . $ticker['last'] . PHP_EOL;
    }
}
catch(\Exception $e)
{
    print_stacktrace($e);
}


