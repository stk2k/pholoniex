<?php
namespace Pholoniex;

use NetDriver\Http\HttpRequest;
use NetDriver\NetDriverInterface;

use Pholoniex\Exception\PholoniexClientExceptionInterface;

/**
 * Pholoniex interface
 */
interface PholoniexClientInterface
{
    /**
     * get last request
     *
     * @return HttpRequest
     */
    public function getLastRequest();

    /**
     * add net driver change listener
     *
     * @param NetDriverChangeListenerInterface|callable $listener
     */
    public function addNetDriverChangeListener($listener);

    /**
     * get net driver
     *
     * @return NetDriverInterface
     */
    public function getNetDriver();

    /**
     * set net driver
     *
     * @param NetDriverInterface $net_driver
     */
    public function setNetDriver(NetDriverInterface $net_driver);

    /**
     * [public] Returns the ticker for all markets
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getTicker();
    
    /**
     * [public] Returns the 24-hour volume for all markets, plus totals for primary currencies.
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getVolume24h();
    
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
    public function getOrderBook($currency_pair = NULL, $depth = NULL);
    
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
    public function getTradeHistory($currency_pair, $start = NULL, $end = NULL);
    
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
    public function getChartData($currency_pair, $start = NULL, $end = NULL, $period = NULL);
    
    /**
     * [public] Returns information about currencies
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getCurrencies();
    
    /**
     * [public] Returns information about currencies
     *
     * @param string $currency
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getLoanOrders($currency);
    
    /**
     * [trading] Returns all of your available balances
     *
     * @return array
     *
     * @throws PholoniexClientExceptionInterface
     */
    public function getBalances();

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
    public function buy($pair, $rate, $amount);


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
    public function sell($pair, $rate, $amount);
}