<?php

namespace PriceTracker\Trackers;

use PriceTracker\Utils\CsvUtils;
use PriceTracker\bo\TrackingData;

abstract class BasePriceTracker {

	private $exchangeName;
	private $priceDataFileName;
	private $priceDataFileHeader = ['Date', 'Buy', 'Sell'];
	private $apiWaitTime = 120;
	private $lastTrackingData;
	private $apiTrackingData;

	abstract protected function getApiTrackingData();

	public function __construct($exchangeName) {
		$this->exchangeName = $exchangeName;
		$this->priceDataFileName = $this->exchangeName . 'PriceData.csv';
		$this->initPriceDataCsv();
		$this->lastTrackingData = $this->getLastTrackingData();
	}

	private function initPriceDataCsv() {
		if (!CsvUtils::checkIfFileExists($this->priceDataFileName)) {
      CsvUtils::writeCsvToFile($this->priceDataFileName, $this->priceDataFileHeader);
    }
	}

	private function getLastTrackingData() {
		$lastLineArray = CsvUtils::getLastLineFromCsvAsArray($this->priceDataFileName);
		if ($this->priceDataFileHeader === $lastLineArray) {
			return new TrackingData();
		}
		$lastTrackingData = new TrackingData();
		$lastTrackingData->setDateTime($lastLineArray[0]);
		$lastTrackingData->setBuyPrice($lastLineArray[1]);
		$lastTrackingData->setSellPrice($lastLineArray[2]);
		return $lastTrackingData;
	}

	private function shouldTrack() {
		if (time() - strtotime($this->lastTrackingData->getDateTime()) > $this->apiWaitTime) {
			return true;
		}
		return false;
	}

	private function shouldLogPriceData() {

    if (strcmp($this->lastTrackingData->getBuyPrice(), $this->apiTrackingData->getBuyPrice()) !== 0
    	|| strcmp($this->lastTrackingData->getSellPrice(), $this->apiTrackingData->getSellPrice()) !== 0) {
        return true;
    }
    return false;
	}

	private function logPriceData() {
    CsvUtils::writeCsvToFile($this->priceDataFileName, $this->apiTrackingData->toArray());
	}

	public function track() {
		if(!$this->shouldTrack()) {
			return "api not called as tracking not necessary currently";
		}

		$this->apiTrackingData = $this->getApiTrackingData();

		if (!$this->shouldLogPriceData()) {
			return "api called, but logging not required as Price data is same as last";
		}

		$this->logPriceData();

		return "Logged Price Data Successfully";
	}
}