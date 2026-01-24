<?php

namespace PriceTracker;

use Exception;
use PriceTracker\Trackers\TimeTracker;
use PriceTracker\Trackers\CryptocurrencyPriceTracker;
use PriceTracker\Utils\AccessLogUtils;
use PriceTracker\Utils\CsvUtils;
use PriceTracker\Config\Exchange;
use GuzzleHttp\Client;


class PriceTrackerScript implements ScriptInterface {

  private $exchangeConfig;
  private $timeTracker;
  private $accessLogFileName;
  private $priceTracker;
  private $accessLogUtils;

	public function __construct($exchangeName, $coinName) {
    $this->timeTracker = new TimeTracker();

    $exchange = new Exchange();
    $this->exchangeConfig = $exchange->getExchangeConfig($exchangeName, $coinName);

    $config = require __DIR__ . '/Config/config.php';
    $csvUtils = new CsvUtils($config['data_path']);
    $this->accessLogUtils = new AccessLogUtils($csvUtils);
    $httpClient = new Client(['timeout' => 30]);

    $this->priceTracker = new CryptocurrencyPriceTracker($this->exchangeConfig, $csvUtils, $httpClient);
  }

  private function start() {
    $this->timeTracker->startTracking();
    echo $this->priceTracker->track();
  }

  private function end() {
    $this->timeTracker->stopTracking();
    $this->accessLogUtils->log(
      $this->exchangeConfig['exchange'] . "-" . $this->exchangeConfig['coin'],
      $this->timeTracker->getTrackingData()
    );
  }

  public function run() {
    $this->start();
    $this->end();
  }
}