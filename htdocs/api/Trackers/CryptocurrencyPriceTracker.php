<?php

namespace PriceTracker\Trackers;

use PriceTracker\Utils\CsvUtils;
use PriceTracker\bo\TrackingData;

class CryptocurrencyPriceTracker {

	private $exchangeConfig;
	private $priceDataFileHeader = ['Date', 'Buy', 'Sell'];
	private $priceDataFileName;
	private $priceDataFileNameSuffix = 'PriceData.csv';
	private $lastTrackingData;
	private $apiTrackingData;

	public function __construct($exchangeConfig) {
		$this->exchangeConfig = $exchangeConfig;
		$this->priceDataFileName = strtolower($this->exchangeConfig['exchange'])
																. ucfirst($this->exchangeConfig['coin'])
																. $this->priceDataFileNameSuffix;
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

		if ($lastLineArray === $this->priceDataFileHeader
				|| empty($lastLineArray)
				|| strtotime($lastLineArray[0]) === false) {
			return new TrackingData();
		}

		$lastTrackingData = new TrackingData();
		$lastTrackingData->setDateTime($lastLineArray[0]);
		$lastTrackingData->setBuyPrice($lastLineArray[1]);
		$lastTrackingData->setSellPrice($lastLineArray[2]);
		return $lastTrackingData;
	}

	public function track() {
		if(!$this->shouldTrack()) {
			return "api not called as tracking not necessary currently";
		}

		$this->setApiTrackingData();

		if (!$this->shouldLogPriceData()) {
			return "api called, but not logging due to some issue";
		}

		$this->logPriceData();
		return "Logged Price Data Successfully";
	}

	private function shouldTrack() {
		if ($this->lastTrackingData->isEmpty()) {
			return true;
		}
		$timeSinceLastTrackingData = time() - strtotime($this->lastTrackingData->getDateTime());
		if ($timeSinceLastTrackingData > $this->exchangeConfig['api_wait']) {
			return true;
		}
		return false;
	}

	private function setApiTrackingData() {
		$apiData = file_get_contents($this->exchangeConfig['url']);
		if ($this->exchangeConfig['datatype'] === 'json') {
			$dataArray = json_decode($apiData, true);
		} else {
			throw new Exception("API Datatype not supported: " . $this->exchangeConfig['datatype']);
		}
		$buyPrice = $this->getPriceFromApiDataArray($dataArray, $this->exchangeConfig['buy_key']);
		$sellPrice = $this->getPriceFromApiDataArray($dataArray, $this->exchangeConfig['sell_key']);
		if (empty($buyPrice) || empty($sellPrice)) {
			$this->apiTrackingData = new TrackingData();
		}
		$apiTrackingData = new TrackingData();
		$apiTrackingData->setDateTime(date(DATE_ISO8601));
		$apiTrackingData->setBuyPrice($buyPrice);
		$apiTrackingData->setSellPrice($sellPrice);
		$this->apiTrackingData = $apiTrackingData;
	}

	private function getPriceFromApiDataArray($dataArray, $exchangePriceKey) {
		$priceKeys = explode('||', $exchangePriceKey);
		$price = $dataArray;
		foreach ($priceKeys as $priceKey) {
			$price = $price[$priceKey];
		}
		return $price;
	}

	private function shouldLogPriceData() {
		if ($this->apiTrackingData->isEmpty()) {
			return false;
		}
    if (strcmp($this->lastTrackingData->getBuyPrice(), $this->apiTrackingData->getBuyPrice()) !== 0
    	|| strcmp($this->lastTrackingData->getSellPrice(), $this->apiTrackingData->getSellPrice()) !== 0) {
        return true;
    }
    return false;
	}

	private function logPriceData() {
    CsvUtils::writeCsvToFile($this->priceDataFileName, $this->apiTrackingData->toArray());
	}
}