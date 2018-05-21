<?php
namespace Pholoniex;

use NetDriver\NetDriverInterface;
use NetDriver\NetDriverHandleInterface;
use NetDriver\Http\HttpRequest;
use NetDriver\Http\HttpGetRequest;
use NetDriver\Http\HttpPostRequest;
use NetDriver\NetDriver\Curl\CurlNetDriver;

use Pholoniex\Exception\PholoniexClientException;
use Pholoniex\Exception\PholoniexClientExceptionInterface;
use Pholoniex\Exception\ServerResponseFormatException;
use Pholoniex\Exception\WebApiCallException;

/**
 * Pholoniex client class
 */
class PholoniexClient implements PholoniexClientInterface
{
    /** @var null|string  */
    private $api_key;

    /** @var null|string  */
    private $api_secret;

    /** @var NetDriverHandleInterface  */
    private $netdriver_handle;

    /** @var HttpRequest  */
    private $last_request;

    /** @var int  */
    private $time_offset;

    /** @var NetDriverInterface */
    private $net_driver;

    /** @var NetDriverChangeListenerInterface[] */
    private $listeners;

    /**
     * construct
     *
     * @param string|null $api_key
     * @param string|null $api_secret
     */
    public function __construct($api_key = null, $api_secret = null){
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->netdriver_handle = null;
        $this->last_request = null;
        $this->time_offset = 0;
        $this->listeners = [];
    }
    
    /**
     * get last request
     *
     * @return HttpRequest
     */
    public function getLastRequest()
    {
        return $this->last_request;
    }

    /**
     * add net driver change listener
     *
     * @param NetDriverChangeListenerInterface|callable $listener
     */
    public function addNetDriverChangeListener($listener)
    {
        if (is_callable($listener) || $listener instanceof NetDriverChangeListenerInterface)
            $this->listeners[] = $listener;
    }

    /**
     * set net driver
     *
     * @param NetDriverInterface $net_driver
     */
    public function setNetDriver(NetDriverInterface $net_driver)
    {
        $this->net_driver = $net_driver;

        // callback
        $this->fireNetDriverChangeEvent($net_driver);
    }

    /**
     * net driver change callback
     *
     * @param NetDriverInterface $net_driver
     */
    private function fireNetDriverChangeEvent(NetDriverInterface $net_driver)
    {
        foreach($this->listeners as $l) {
            if ($l instanceof NetDriverChangeListenerInterface) {
                $l->onNetDriverChanged($net_driver);
            }
            else if (is_callable($l)) {
                $l($net_driver);
            }
        }
    }

    /**
     * get net friver
     *
     * @return CurlNetDriver|NetDriverInterface
     */
    public function getNetDriver()
    {
        if ($this->net_driver){
            return $this->net_driver;
        }
        $this->net_driver = new CurlNetDriver();
        // callback
        $this->fireNetDriverChangeEvent($this->net_driver);
        return $this->net_driver;
    }

    /**
     * get net driver handle
     *
     * @return NetDriverHandleInterface|null
     */
    public function getNetDriverHandle()
    {
        if ($this->netdriver_handle){
            return $this->netdriver_handle;
        }
        $this->netdriver_handle = $this->getNetDriver()->newHandle();
        return $this->netdriver_handle;
    }

    /**
     * make request URL
     *
     * @param string $api
     * @param array $query_data
     *
     * @return string
     */
    private static function getURL($api, array $query_data = null)
    {
        $url = PholoniexApi::ENDPOINT . $api;
        if ($query_data){
            $url .= '&' . http_build_query($query_data);
        }
        return $url;
    }

    /**
     * call web API by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     *
     * @return mixed
     *
     * @throws PholoniexClientExceptionInterface
     */
    private function get($api, array $query_data = [])
    {
        $query_data = is_array($query_data) ? array_filter($query_data, function($v){
            return $v !== null;
        }) : null;

        $url = self::getURL($api, $query_data);
        $request = new HttpGetRequest($this->getNetDriver(), $url, $query_data);

        return $this->executeRequest($request);
    }

    /**
     * call web API(private) by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     *
     * @return mixed
     *
     * @throws PholoniexClientExceptionInterface
     */
    /*
    private function privateGet($api, array $query_data = [])
    {
        $query_data = array_filter($query_data, function($v){
            return $v !== null;
        });
        
        $mt = explode(' ', microtime());
        $query_data['nonce'] = $mt[1].substr($mt[0], 2, 6);
        
        $query = http_build_query($query_data, '', '&');
        $signature = hash_hmac('sha512', $query, $this->api_secret);
    
        $options['http-headers'] = array(
            'Key: ' . $this->api_key,
            'Sign: ' . $signature,
        );
        
        $url = self::getURL($api, $query_data);
        $request = new HttpGetRequest($this->getNetDriver(), $url, $options);
    
        return $this->executeRequest($request);
    }
    */
    
    /**
     * call web API(private) by HTTP/POST
     *
     * @param string $api
     * @param array $post_data
     *
     * @return mixed
     *
     * @throws PholoniexClientExceptionInterface
     */
    private function privatePost($api, array $post_data = [])
    {
        $post_data = array_filter($post_data, function($v){
            return $v !== null;
        });
    
        $mt = explode(' ', microtime());
        $post_data['nonce'] = $mt[1].substr($mt[0], 2, 6);
    
        $query = http_build_query($post_data, '', '&');
        $signature = hash_hmac('sha512', $query, $this->api_secret);
        
        $options['http-headers'] = array(
            'Key' => $this->api_key,
            'Sign' => $signature,
        );
        
        $url = self::getURL($api, $post_data);
        $request = new HttpPostRequest($this->getNetDriver(), $url, $post_data, $options);
    
        return $this->executeRequest($request);
    }
    
    /**
     * call web API(private) by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     *
     * @return mixed
     *
     * @throws PholoniexClientExceptionInterface
     */
    /*
    private function privateDelete($api, array $query_data = [])
    {
        $query_data = array_filter($query_data, function($v){
            return $v !== null;
        });
        
        $ts = (microtime(true)*1000) + $this->time_offset;
        $query_data['timestamp'] = number_format($ts,0,'.','');
        $query = http_build_query($query_data, '', '&');
        $signature = hash_hmac('sha256', $query, $this->api_secret);
        
        $options['http-headers'] = array(
            'X-MBX-APIKEY' => $this->api_key,
        );
        //$options['verbose'] = 1;
        
        $url = self::getURL($api) . '?' . $query . '&signature=' . $signature;
        $request = new HttpDeleteRequest($this->getNetDriver(), $url,  $options);
        
        return $this->executeRequest($request);
    }
    */
    
    /**
     * execute request
     *
     * @param HttpRequest $request
     *
     * @return mixed
     *
     * @throws PholoniexClientExceptionInterface
     */
    private function executeRequest($request)
    {
        try{
            $response = $this->net_driver->sendRequest($this->getNetDriverHandle(), $request);

            $this->last_request = $request;

            $json = @json_decode($response->getBody(), true);
            if ($json === null){
                throw new WebApiCallException(json_last_error_msg());
            }
            return $json;
        }
        catch(\Throwable $e)
        {
            throw new PholoniexClientException('NetDriver#sendRequest() failed: ' . $e->getMessage(), $e);
        }
    }
    
    /**
     * [public] Returns the ticker for all markets
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getTicker()
    {
        // HTTP GET
        $json = $this->get(PholoniexApi::TICKER);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    
    /**
     * [public] Returns the 24-hour volume for all markets, plus totals for primary currencies.
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getVolume24h()
    {
        // HTTP GET
        $json = $this->get(PholoniexApi::VOLUME_24H);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    /**
     * [public] Returns the order book for a given market, as well as a sequence number for use with the Push API and an indicator specifying whether the market is frozen
     *
     * @param string $currency_pair
     * @param int $depth
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getOrderBook($currency_pair = NULL, $depth = NULL)
    {
        $data = array(
            'currencyPair' => $currency_pair ? $currency_pair : 'all',
        );
        if ($depth){
            $data['depth'] = $depth;
        }
        
        // HTTP GET
        $json = $this->get(PholoniexApi::ORDER_BOOK, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    /**
     * [public] Returns the past 200 trades for a given market, or up to 50,000 trades between a range specified in UNIX timestamps by the "start" and "end" GET parameters
     *
     * @param string $currency_pair
     * @param int $start
     * @param int $end
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getTradeHistory($currency_pair, $start = NULL, $end = NULL)
    {
        $data = array(
            'currencyPair' => $currency_pair ? $currency_pair : 'all',
        );
        if ($start){
            $data['start'] = $start;
        }
        if ($end){
            $data['end'] = $end;
        }
        
        // HTTP GET
        $json = $this->get(PholoniexApi::TRADE_HISTORY, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    /**
     * [public] Returns candlestick chart data
     *
     * @param string $currency_pair
     * @param int $start
     * @param int $end
     * @param int $period
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getChartData($currency_pair, $start = NULL, $end = NULL, $period = NULL)
    {
        $data = array(
            'currencyPair' => $currency_pair ? $currency_pair : 'all',
        );
        if ($period){
            $data['period'] = $period;
        }
        if ($start){
            $data['start'] = $start;
        }
        if ($end){
            $data['end'] = $end;
        }
    
        // HTTP GET
        $json = $this->get(PholoniexApi::CHART_DATA, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    /**
     * [public] Returns information about currencies
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getCurrencies()
    {
        // HTTP GET
        $json = $this->get(PholoniexApi::CURRENCIES);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    /**
     * [public] Returns information about currencies
     *
     * @param string $currency
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getLoanOrders($currency)
    {
        $data = array(
            'currency' => $currency,
        );
        // HTTP GET
        $json = $this->get(PholoniexApi::LOAN_ORDERS, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
    
    
    /**
     * [trading] Returns all of your available balances
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getBalances()
    {
        $data = array(
            'command' => 'returnBalances',
        );
        // HTTP POST
        $json = $this->privatePost(PholoniexApi::TRADING_API, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }

    /**
     * [trading] Places a limit order in a given market.
     *
     * @param string $pair
     * @param float $rate
     * @param float $amount
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function buy($pair, $rate, $amount)
    {
        $data = array(
            'command' => 'buy',
            'currencyPair' => strtoupper($pair),
            'rate' => $rate,
            'amount' => $amount,
            'fillOrKill' => 1,
        );
        // HTTP POST
        $json = $this->privatePost(PholoniexApi::TRADING_API, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }

    /**
     * [trading] Places a limit order in a given market.
     *
     * @param string $pair
     * @param float $rate
     * @param float $amount
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function sell($pair, $rate, $amount)
    {
        $data = array(
            'command' => 'sell',
            'currencyPair' => strtoupper($pair),
            'rate' => $rate,
            'amount' => $amount,
        );
        // HTTP POST
        $json = $this->privatePost(PholoniexApi::TRADING_API, $data);
        // check return type
        if (!is_array($json)){
            throw new ServerResponseFormatException('response must be an array, but returned:' . gettype($json));
        }
        return $json;
    }
}