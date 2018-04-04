<?php
namespace Pholoniex;

/**
 * Pholoniex API consts class
 */
final class PholoniexApi
{
    const ENDPOINT      = 'https://poloniex.com';
    
    // poloniex public API
    const TICKER          = '/public?command=returnTicker';
    const VOLUME_24H      = '/public?command=return24hVolume';
    const ORDER_BOOK      = '/public?command=returnOrderBook';
    const TRADE_HISTORY   = '/public?command=returnTradeHistory';
    const CHART_DATA      = '/public?command=returnChartData';
    const CURRENCIES      = '/public?command=returnCurrencies';
    const LOAN_ORDERS     = '/public?command=returnLoanOrders';
    
    // poloniex trading API
    const TRADING_API     = '/tradingApi';
    
}