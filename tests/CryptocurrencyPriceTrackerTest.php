<?php

use PHPUnit\Framework\TestCase;
use PriceTracker\Trackers\CryptocurrencyPriceTracker;
use PriceTracker\Utils\CsvUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PriceTracker\bo\TrackingData;

class CryptocurrencyPriceTrackerTest extends TestCase {

    public function testTrackSuccessfully() {
        $exchangeConfig = [
            'exchange' => 'test_exchange',
            'coin' => 'test_coin',
            'url' => 'http://test.com',
            'datatype' => 'json',
            'api_wait' => 0,
            'buy_key' => 'buy',
            'sell_key' => 'sell'
        ];

        // Mock CsvUtils
        $csvUtilsMock = $this->createMock(CsvUtils::class);
        $csvUtilsMock->method('checkIfFileExists')->willReturn(true);
        $csvUtilsMock->method('getLastLineFromCsvAsArray')->willReturn(
            ['2023-01-01T00:00:00+0000', '100', '105']
        );
        $csvUtilsMock->expects($this->once())
            ->method('writeCsvToFile')
            ->with(
                $this->stringContains('PriceData.csv'),
                $this->callback(function($array) {
                    return $array[1] == '200' && $array[2] == '210';
                })
            );

        // Mock Guzzle
        $mock = new MockHandler([
            new Response(200, [], json_encode(['buy' => 200, 'sell' => 210]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $tracker = new CryptocurrencyPriceTracker($exchangeConfig, $csvUtilsMock, $client);

        $result = $tracker->track();

        $this->assertEquals("Logged Price Data Successfully", $result);
    }

    public function testTrackNoChange() {
        $exchangeConfig = [
            'exchange' => 'test_exchange',
            'coin' => 'test_coin',
            'url' => 'http://test.com',
            'datatype' => 'json',
            'api_wait' => 0,
            'buy_key' => 'buy',
            'sell_key' => 'sell'
        ];

        // Mock CsvUtils
        $csvUtilsMock = $this->createMock(CsvUtils::class);
        $csvUtilsMock->method('checkIfFileExists')->willReturn(true);
        $csvUtilsMock->method('getLastLineFromCsvAsArray')->willReturn(
            ['2023-01-01T00:00:00+0000', '100', '105']
        );
        $csvUtilsMock->expects($this->never())
            ->method('writeCsvToFile');

        // Mock Guzzle - same price
        $mock = new MockHandler([
            new Response(200, [], json_encode(['buy' => 100, 'sell' => 105]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $tracker = new CryptocurrencyPriceTracker($exchangeConfig, $csvUtilsMock, $client);

        $result = $tracker->track();

        $this->assertEquals("api called, but not logging due to some issue", $result);
    }
}
