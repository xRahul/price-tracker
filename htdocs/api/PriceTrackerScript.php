<?php

namespace PriceTracker;

use Exception;
use PriceTracker\Trackers\TimeTracker;
use PriceTracker\Trackers\CryptocurrencyPriceTracker;
use PriceTracker\Utils\AccessLogUtils;
use PriceTracker\Config\Exchange;


class PriceTrackerScript implements ScriptInterface {

  private $exchangeConfig;
  private $timeTracker;
  private $accessLogFileName;

	public function __construct($exchangeName, $coinName) {
    $this->timeTracker = new TimeTracker();
    $this->exchangeConfig = Exchange::getExchangeConfig($exchangeName, $coinName);
    $this->priceTracker = new CryptocurrencyPriceTracker($this->exchangeConfig);
  }

  private function start() {
    $this->timeTracker->startTracking();
    echo $this->priceTracker->track();
  }

  private function end() {
    $this->timeTracker->stopTracking();
    AccessLogUtils::log(
      $this->exchangeConfig['exchange'] . "-" . $this->exchangeConfig['coin'],
      $this->timeTracker->getTrackingData()
    );
  }

  public function run() {
    $this->start();
    $this->end();
  }
}