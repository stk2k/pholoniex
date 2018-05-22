<?php
namespace Pholoniex;

use NetDriver\Http\HttpRequest;
use NetDriver\NetDriverInterface;

use Psr\Log\LoggerInterface;

use Pholoniex\Exception\PholoniexClientExceptionInterface;

/**
 * Logger decorator
 */
class PholoniexLoggerClient implements PholoniexClientInterface, NetDriverChangeListenerInterface
{
    /** @var PholoniexClientInterface  */
    private $client;
    
    /** @var LoggerInterface */
    private $logger;
    
    /**
     * construct
     *
     * @param PholoniexClientInterface $client
     * @param LoggerInterface $logger
     */
    public function __construct($client, $logger){
        $this->client = $client;
        $this->logger = $logger;

        $client->addNetDriverChangeListener($this);
    }

    /**
     * get last request
     *
     * @return HttpRequest
     */
    public function getLastRequest()
    {
        return $this->client->getLastRequest();
    }

    /**
     * Net driver change callback
     *
     * @param NetDriverInterface $net_driver
     */
    public function onNetDriverChanged(NetDriverInterface $net_driver)
    {
        $net_driver->setLogger($this->logger);
    }

    /**
     * add net driver change listener
     *
     * @param NetDriverChangeListenerInterface|callable $listener
     */
    public function addNetDriverChangeListener($listener)
    {
        $this->client->addNetDriverChangeListener($listener);
    }

    /**
     * get net driver
     *
     * @return NetDriverInterface
     */
    public function getNetDriver()
    {
        return $this->client->getNetDriver();
    }

    /**
     * set net driver
     *
     * @param NetDriverInterface $net_driver
     */
    public function setNetDriver(NetDriverInterface $net_driver)
    {
        $this->client->setNetDriver($net_driver);
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
        $this->logger->debug('started getTicker');
        $ret = $this->client->getTicker();
        $this->logger->debug('finished getTicker');
        return $ret;
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
        $this->logger->debug('started getVolume24h');
        $ret = $this->client->getVolume24h();
        $this->logger->debug('finished getVolume24h');
        return $ret;
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
        $this->logger->debug('started getOrderBook');
        $ret = $this->client->getOrderBook($currency_pair, $depth);
        $this->logger->debug('finished getOrderBook');
        return $ret;
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
        $this->logger->debug('started getTradeHistory');
        $ret = $this->client->getTradeHistory($currency_pair, $start, $end);
        $this->logger->debug('finished getTradeHistory');
        return $ret;
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
        $this->logger->debug('started getChartData');
        $ret = $this->client->getChartData($currency_pair, $start, $end, $period);
        $this->logger->debug('finished getChartData');
        return $ret;
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
        $this->logger->debug('started getCurrencies');
        $ret = $this->client->getCurrencies();
        $this->logger->debug('finished getCurrencies');
        return $ret;
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
        $this->logger->debug('started getLoanOrders');
        $ret = $this->client->getLoanOrders($currency);
        $this->logger->debug('finished getLoanOrders');
        return $ret;
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
        $this->logger->debug('started getBalances');
        $ret = $this->client->getBalances();
        $this->logger->debug('finished getBalances');
        return $ret;
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
        $this->logger->debug('started buy');
        $ret = $this->client->buy($pair, $rate, $amount);
        $this->logger->debug('finished buy');
        return $ret;
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
        $this->logger->debug('started sell');
        $ret = $this->client->sell($pair, $rate, $amount);
        $this->logger->debug('finished sell');
        return $ret;
    }
}