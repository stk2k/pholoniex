<?php
namespace Pholoniex;

use Pholoniex\Exception\ApiClientException;
use Pholoniex\Exception\ServerResponseFormatException;

/**
 * Pholoniex interface
 */
interface IPholoniexClient
{
    /**
     * [public] Returns the ticker for all markets
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getTicker();
    
    /**
     * [public] Returns the 24-hour volume for all markets, plus totals for primary currencies.
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getVolume24h();
    
    /**
     * [public] Returns the order book for a given market, as well as a sequence number for use with the Push API and an indicator specifying whether the market is frozen
     *
     * @param string $currency_pair
     * @param int $depth
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getOrderBook($currency_pair = NULL, $depth = NULL);
    
    /**
     * [public] Returns the past 200 trades for a given market, or up to 50,000 trades between a range specified in UNIX timestamps by the "start" and "end" GET parameters
     *
     * @param string $currency_pair
     * @param int $start
     * @param int $end
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getTradeHistory($currency_pair, $start = NULL, $end = NULL);
    
    /**
     * [public] Returns candlestick chart data
     *
     * @param string $currency_pair
     * @param int $start
     * @param int $end
     * @param int $period
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getChartData($currency_pair, $start = NULL, $end = NULL, $period = NULL);
    
    /**
     * [public] Returns information about currencies
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getCurrencies();
    
    /**
     * [public] Returns information about currencies
     *
     * @param string $currency
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getLoanOrders($currency);
    
    /**
     * [trading] Returns all of your available balances
     *
     * @return object
     *
     * @throws ServerResponseFormatException
     * @throws ApiClientException
     */
    public function getBalances();
}