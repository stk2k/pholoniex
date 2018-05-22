<?php
use NetDriver\Http\HttpGetRequest;

use Pholoniex\PholoniexClient;
use Pholoniex\PholoniexApi;

class PholoniexClientTest extends PHPUnit_Framework_TestCase
{
    /** @var PholoniexClient */
    private $client;
    
    protected function setUp()
    {
        $api_key = getenv('POLONIEX_API_KEY');
        $api_secret = getenv('POLONIEX_API_SECRET');
        
        $this->assertGreaterThan(0,strlen($api_key),'Plase set environment variable(POLONIEX_API_KEY) before running this test.');
        $this->assertGreaterThan(0,strlen($api_secret),'Plase set environment variable(POLONIEX_API_SECRET) before running this test.');
        
        $this->client = new PholoniexClient($api_key, $api_secret);
    
        sleep(1);
    }

    public function testGetTicker()
    {
        $ticker = $this->client->getTicker();
    
        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();
    
        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::TICKER, $req->getUrl() );
        $this->assertInternalType('array', $ticker );
        $this->assertSame(true, isset($ticker['BTC_ETH']) );
        $this->assertInternalType('array', $ticker['BTC_ETH'] );
        $this->assertSame(true, isset($ticker['BTC_ETH']['id']) );
        $this->assertSame(true, isset($ticker['BTC_ETH']['last']) );
        $this->assertSame(true, isset($ticker['BTC_ETH']['lowestAsk']) );
        $this->assertSame(true, isset($ticker['BTC_ETH']['highestBid']) );
    }

    public function testGetVolume24h()
    {
        $volume24h = $this->client->getVolume24h();

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::VOLUME_24H, $req->getUrl() );
        $this->assertInternalType('array', $volume24h );
        $this->assertSame(true, isset($volume24h['BTC_ETH']) );
        $this->assertInternalType('array', $volume24h['BTC_ETH'] );
        $this->assertSame(['BTC','ETH'], array_keys($volume24h['BTC_ETH']) );
    }

    public function testGetOrderBook()
    {
        $order_book = $this->client->getOrderBook('BTC_ETH');

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::ORDER_BOOK . '&currencyPair=BTC_ETH', $req->getUrl() );
        $this->assertInternalType('array', $order_book );
        $this->assertSame(true, isset($order_book['asks']) );
        $this->assertSame(true, isset($order_book['bids']) );
        $this->assertSame(true, isset($order_book['isFrozen']) );
        $this->assertSame(true, isset($order_book['seq']) );
        $this->assertInternalType('array', $order_book['asks'] );
        $this->assertInternalType('array', $order_book['bids'] );
    }

    public function testGetTradeHistory()
    {
        $history = $this->client->getTradeHistory('BTC_ETH');

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::TRADE_HISTORY . '&currencyPair=BTC_ETH', $req->getUrl() );
        $this->assertInternalType('array', $history );
        $this->assertSame(true, isset($history[0]) );
        $this->assertInternalType('array', $history[0] );
        $this->assertSame(true, isset($history[0]['tradeID']) );
        $this->assertSame(true, isset($history[0]['date']) );
        $this->assertSame(true, isset($history[0]['type']) );
        $this->assertSame(true, isset($history[0]['rate']) );
        $this->assertSame(true, isset($history[0]['amount']) );
        $this->assertSame(true, isset($history[0]['total']) );
    }

    public function testGetChartData()
    {
        $end = time();
        $start = $end - 3600;
        $chart = $this->client->getChartData('BTC_ETH', $start, $end, 300);

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $expected_url_pattern = preg_quote(PholoniexApi::ENDPOINT . PholoniexApi::CHART_DATA) . '&currencyPair=BTC_ETH&period=300&start=([0-9]*)&end=([0-9]*)';
        $this->assertRegExp('@^' . $expected_url_pattern . '$@', $req->getUrl() );
        $this->assertInternalType('array', $chart );
        $this->assertSame(12, count($chart) );
        $this->assertSame(true, isset($chart[0]) );
        $this->assertInternalType('array', $chart[0] );
        $this->assertSame(true, isset($chart[0]['date']) );
        $this->assertSame(true, isset($chart[0]['high']) );
        $this->assertSame(true, isset($chart[0]['low']) );
        $this->assertSame(true, isset($chart[0]['open']) );
        $this->assertSame(true, isset($chart[0]['close']) );
        $this->assertSame(true, isset($chart[0]['volume']) );
    }

    public function testGetCurrencies()
    {
        $currencies = $this->client->getCurrencies();

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::CURRENCIES, $req->getUrl() );
        $this->assertInternalType('array', $currencies );
        $this->assertSame(true, isset($currencies['BTC']) );
        $this->assertInternalType('array', $currencies['BTC'] );
        $this->assertSame(true, isset($currencies['BTC']['name']) );
        $this->assertSame('Bitcoin', $currencies['BTC']['name'] );
        $this->assertSame(true, isset($currencies['BTC']['disabled']) );
        $this->assertSame(true, isset($currencies['BTC']['delisted']) );
        $this->assertSame(true, isset($currencies['BTC']['frozen']) );
        $this->assertSame(true, isset($currencies['BTC']['minConf']) );
        $this->assertSame(true, isset($currencies['BTC']['txFee']) );
    }

    public function testGetLoanOrders()
    {
        $orders = $this->client->getLoanOrders('BTC');

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $this->assertEquals(PholoniexApi::ENDPOINT . PholoniexApi::LOAN_ORDERS . '&currency=BTC', $req->getUrl() );
        $this->assertInternalType('array', $orders );
        $this->assertSame(2, count($orders) );
        $this->assertSame(true, isset($orders['offers']) );
        $this->assertSame(true, isset($orders['demands']) );
        $this->assertInternalType('array', $orders['offers'] );
        $this->assertGreaterThanOrEqual(0, count($orders['offers']) );
        $this->assertSame(true, isset($orders['offers'][0]['rate']) );
        $this->assertSame(true, isset($orders['offers'][0]['amount']) );
        $this->assertSame(true, isset($orders['offers'][0]['rangeMin']) );
        $this->assertSame(true, isset($orders['offers'][0]['rangeMax']) );
        $this->assertInternalType('array', $orders['demands'] );
        $this->assertGreaterThanOrEqual(0, count($orders['demands']) );
        $this->assertSame(true, isset($orders['demands'][0]['rate']) );
        $this->assertSame(true, isset($orders['demands'][0]['amount']) );
        $this->assertSame(true, isset($orders['demands'][0]['rangeMin']) );
        $this->assertSame(true, isset($orders['demands'][0]['rangeMax']) );
    }

    public function testGetBalances()
    {
        $balances = $this->client->getBalances();

        /** @var HttpGetRequest $req */
        $req = $this->client->getLastRequest();

        $expected_url_pattern = preg_quote(PholoniexApi::ENDPOINT . PholoniexApi::TRADING_API . '?') . 'command=returnBalances&nonce=([0-9]*)';
        $this->assertRegExp('@^' . $expected_url_pattern . '$@', $req->getUrl() );
        $this->assertInternalType('array', $balances );
        $this->assertSame(true, isset($balances['BTC']) );
        $this->assertSame(true, isset($balances['ETH']) );
    }
}