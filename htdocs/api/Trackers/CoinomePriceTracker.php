<?php

namespace PriceTracker\Trackers;

use PriceTracker\Trackers\BasePriceTracker;
use PriceTracker\bo\TrackingData;


class CoinomePriceTracker extends BasePriceTracker {

	private $apiUrl;

	public function __construct() {
		parent::__construct('coinome');
		$this->apiUrl =  'https://www.coinome.com/api/v1/ticker.json';
	}

	protected function getApiTrackingData() {
		$json = json_decode(file_get_contents($this->apiUrl), true);

		$apiTrackingData = new TrackingData();
		$apiTrackingData->setDateTime(date(DATE_ISO8601));
		$apiTrackingData->setBuyPrice($json['BTC-INR']['lowest_ask']);
		$apiTrackingData->setSellPrice($json['BTC-INR']['highest_bid']);
		return $apiTrackingData;
	}
}