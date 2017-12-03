<?php

namespace PriceTracker\Trackers;

use PriceTracker\Trackers\BasePriceTracker;
use PriceTracker\bo\TrackingData;


class ZebpayPriceTracker extends BasePriceTracker {

	private $apiUrl;

	public function __construct() {
		parent::__construct('zebpay');
		$this->apiUrl =  'compress.zlib://https://www.zebapi.com/api/v1/market/ticker/btc/inr';
	}

	protected function getApiTrackingData() {
		$json = json_decode(file_get_contents($this->apiUrl), true);
		$apiTrackingData = new TrackingData();
		$apiTrackingData->setDateTime(date(DATE_ISO8601));
		$apiTrackingData->setBuyPrice($json['buy']);
		$apiTrackingData->setSellPrice($json['sell']);
		return $apiTrackingData;
	}
}