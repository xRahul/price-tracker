<?php

namespace PriceTracker\Trackers;

use PriceTracker\Utils\CsvUtils;
use PriceTracker\bo\TrackingData;
use GuzzleHttp\Client;
use Exception;

class CryptocurrencyPriceTracker {

	private $exchangeConfig;
	private $priceDataFileHeader = ['Date', 'Buy', 'Sell'];
	private $priceDataFileName;
	private $priceDataFileNameSuffix = 'PriceData.csv';
	private $lastTrackingData;
	private $apiTrackingData;
    private $csvUtils;
    private $httpClient;

	public function __construct($exchangeConfig, CsvUtils $csvUtils, Client $httpClient) {
		$this->exchangeConfig = $exchangeConfig;
        $this->csvUtils = $csvUtils;
        $this->httpClient = $httpClient;

		$this->priceDataFileName = strtolower($this->exchangeConfig['exchange'])
																. ucfirst($this->exchangeConfig['coin'])
																. $this->priceDataFileNameSuffix;
		$this->initPriceDataCsv();
		$this->lastTrackingData = $this->getLastTrackingData();
	}

	private function initPriceDataCsv() {
		if (!$this->csvUtils->checkIfFileExists($this->priceDataFileName)) {
             $this->csvUtils->writeCsvToFile($this->priceDataFileName, $this->priceDataFileHeader);
        }
	}

	private function getLastTrackingData() {
        try {
		    $lastLineArray = $this->csvUtils->getLastLineFromCsvAsArray($this->priceDataFileName);
        } catch (Exception $e) {
            $lastLineArray = [];
        }

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
        try {
            $response = $this->httpClient->request('GET', $this->exchangeConfig['url']);
            $apiData = $response->getBody()->getContents();
        } catch (Exception $e) {
             throw new Exception("Error fetching API data: " . $e->getMessage());
        }

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
            if (is_array($price) && array_key_exists($priceKey, $price)) {
			    $price = $price[$priceKey];
            } else {
                return null;
            }
		}
		return $price;
	}

	private function shouldLogPriceData() {
		if ($this->apiTrackingData->isEmpty()) {
			return false;
		}
        // Use stricter comparison or handle potential type mismatches
        // strcmp returns 0 if strings are equal
        $buyDiff = strcmp((string)$this->lastTrackingData->getBuyPrice(), (string)$this->apiTrackingData->getBuyPrice());
        $sellDiff = strcmp((string)$this->lastTrackingData->getSellPrice(), (string)$this->apiTrackingData->getSellPrice());

    if ($buyDiff !== 0 || $sellDiff !== 0) {
        return true;
    }
    return false;
	}

	private function logPriceData() {
        $this->csvUtils->writeCsvToFile($this->priceDataFileName, $this->apiTrackingData->toArray());
	}
}
