<?php
namespace Pholoniex;

use Pholoniex\Exception\ApiClientException;
use Pholoniex\Exception\ServerResponseFormatException;
use Pholoniex\Http\CurlRequest;
use Pholoniex\Http\HttpGetRequest;
use Pholoniex\Http\HttpDeleteRequest;
use Pholoniex\Http\HttpPostRequest;
use Pholoniex\Http\CurlHandle;


/**
 * Pholoniex client class
 */
class PholoniexClient implements IPholoniexClient
{
    const DEFAULT_USERAGENT    = 'pholoniex';
    
    private $api_key;
    private $api_secret;
    private $user_agent;
    private $curl_handle;
    private $last_request;
    private $time_offset;
    
    /**
     * construct
     *
     * @param string|null $api_key
     * @param string|null $api_secret
     */
    public function __construct($api_key = null, $api_secret = null){
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->user_agent = self::DEFAULT_USERAGENT;
        $this->curl_handle = new CurlHandle();
        $this->last_request = null;
        $this->time_offset = 0;
    }
    
    /**
     * get last request
     *
     * @return CurlRequest
     */
    public function getLastRequest()
    {
        return $this->last_request;
    }
    
    /**
     * get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }
    
    /**
     * make request URL
     *
     * @param string $api
     *
     * @return string
     */
    private static function getURL($api)
    {
        return PholoniexApi::ENDPOINT . $api;
    }
    
    /**
     * call web API by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     * @param bool $return_value
     *
     * @return mixed
     *
     * @throws ApiClientException
     */
    private function get($api, $query_data = null, $return_value = true)
    {
        $url = self::getURL($api);
    
        $query_data = is_array($query_data) ? array_filter($query_data, function($v){
            return $v !== null;
        }) : null;
        
        $request = new HttpGetRequest($this, $url, $query_data);
    
        return $this->executeRequest($request, $return_value);
    }
    
    /**
     * call web API(private) by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     * @param bool $return_value
     *
     * @return mixed
     *
     * @throws ApiClientException
     */
    private function privateGet($api, $query_data = null, $return_value = true)
    {
        $query_data = is_array($query_data) ? array_filter($query_data, function($v){
            return $v !== null;
        }) : null;
        
        $mt = explode(' ', microtime());
        $query_data['nonce'] = $mt[1].substr($mt[0], 2, 6);
        
        $query = http_build_query($query_data, '', '&');
        $signature = hash_hmac('sha512', $query, $this->api_secret);
    
        $options['http_headers'] = array(
            'Key: ' . $this->api_key,
            'Sign: ' . $signature,
        );
        
        $url = self::getURL($api);
        $request = new HttpGetRequest($this, $url, $query_data, $options);
    
        return $this->executeRequest($request, $return_value);
    }
    
    /**
     * call web API(private) by HTTP/POST
     *
     * @param string $api
     * @param array $post_data
     * @param bool $return_value
     *
     * @return mixed
     *
     * @throws ApiClientException
     */
    private function privatePost($api, $post_data = null, $return_value = true)
    {
        $post_data = is_array($post_data) ? array_filter($post_data, function($v){
            return $v !== null;
        }) : null;
    
        $mt = explode(' ', microtime());
        $post_data['nonce'] = $mt[1].substr($mt[0], 2, 6);
    
        $query = http_build_query($post_data, '', '&');
        $signature = hash_hmac('sha512', $query, $this->api_secret);
        
        $options['http_headers'] = array(
            'Key' => $this->api_key,
            'Sign' => $signature,
        );
        
        $url = self::getURL($api);
        $request = new HttpPostRequest($this, $url, $post_data, $options);
    
        return $this->executeRequest($request, $return_value);
    }
    
    /**
     * call web API(private) by HTTP/GET
     *
     * @param string $api
     * @param array|null $query_data
     * @param bool $return_value
     *
     * @return mixed
     *
     * @throws ApiClientException
     */
    private function privateDelete($api, $query_data = null, $return_value = true)
    {
        $query_data = is_array($query_data) ? array_filter($query_data, function($v){
            return $v !== null;
        }) : null;
        
        $ts = (microtime(true)*1000) + $this->time_offset;
        $query_data['timestamp'] = number_format($ts,0,'.','');
        $query = http_build_query($query_data, '', '&');
        $signature = hash_hmac('sha256', $query, $this->api_secret);
        
        $options['http_headers'] = array(
            'X-MBX-APIKEY' => $this->api_key,
        );
        //$options['verbose'] = 1;
        
        $url = self::getURL($api) . '?' . $query . '&signature=' . $signature;
        $request = new HttpDeleteRequest($this, $url,  $options);
        
        return $this->executeRequest($request, $return_value);
    }
    
    /**
     * execute request
     *
     * @param CurlRequest $request
     * @param bool $return_value
     *
     * @return mixed
     *
     * @throws ApiClientException
     */
    private function executeRequest($request, $return_value = true)
    {
        $json = $request->execute($this->curl_handle, $return_value);
    
        $this->last_request = $request;
    
        return $json;
    }
    
    /**
     * [public] Returns the ticker for all markets
     *
     * @return array
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws ServerResponseFormatException
     * @throws ApiClientException
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
     * @throws
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
     * @throws
     */
    public function sell($pair, $rate, $amount)
    {
        $data = array(
            'command' => 'sell',
            'pair' => strtoupper($pair),
            'rate' => $rate,
            'amount' => $amount
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