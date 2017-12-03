<?php

namespace PriceTracker;

use Exception;
use PriceTracker\Trackers\TimeTracker;
use PriceTracker\Trackers\CoinomePriceTracker;
use PriceTracker\Trackers\ZebpayPriceTracker;
use PriceTracker\Utils\AccessLogUtils;


class PriceTrackerScript implements ScriptInterface {

  private $supportedExchanges = ['coinome', 'zebpay'];
  private $exchangeName;
  private $timeTracker;
  private $accessLogFileName;

	public function __construct($exchangeName) {
    if (!in_array($exchangeName, $this->supportedExchanges)) {
      throw new Exception('Exchange is Not Supported: ' . $exchangeName);
    }

    $this->timeTracker = new TimeTracker();
    $this->exchangeName = $exchangeName;

    $this->initializePriceTracker();
  }

  private function initializePriceTracker() {
    if ($this->exchangeName === 'coinome') {
      $this->priceTracker = new CoinomePriceTracker();
    }
    if ($this->exchangeName === 'zebpay') {
      $this->priceTracker = new ZebpayPriceTracker();
    }
    if (empty($this->priceTracker)) {
      throw new Exception("PriceTracker can not be initialized. Please check the exchange");
    }
  }

  private function start() {
    $this->timeTracker->startTracking();

    echo $this->priceTracker->track();
  }

  private function end() {
    $this->timeTracker->stopTracking();
    AccessLogUtils::log($this->exchangeName, $this->timeTracker->getTrackingData());
  }

  public function run() {
    $this->start();
    $this->end();
  }
}