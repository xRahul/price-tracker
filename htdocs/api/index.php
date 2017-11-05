<?php

$timeScriptStarts = microtime(true);

$nowDateTime = new DateTime();
$nowWeek = $nowDateTime->format("W");
$nowYear = $nowDateTime->format("Y");
$accessLogFileName = 'access-log-' . $nowWeek . '-' . $nowYear . '.csv';
$priceListFileName = 'zebPayPrices.csv';
$zebPayApiUrl = 'https://www.zebapi.com/api/v1/market/ticker/btc/inr';
$timeZone = 'Asia/Kolkata';
$buyApiPriceKey = 'buy';
$sellApiPriceKey = 'sell';
$dateCsvPriceKey = 'Date';
$buyCsvPriceKey = 'Buy';
$sellCsvPriceKey = 'Sell';
$apiCallWait = 120;



function writeCsvToFile($fileName, $arrayToWrite) {
    $f = fopen($fileName, 'a');
    fputcsv($f, $arrayToWrite);
    fclose($f);
}



function initSettings() {
    global $timeZone;
    global $priceListFileName;
    global $dateCsvPriceKey;
    global $buyCsvPriceKey;
    global $sellCsvPriceKey;

    date_default_timezone_set($timeZone);
    if (!file_exists($priceListFileName)) {
        writeCsvToFile($priceListFileName, [$dateCsvPriceKey, $buyCsvPriceKey, $sellCsvPriceKey]);
    }
}



function getZebPayPriceData() {
    global $zebPayApiUrl;
    global $buyApiPriceKey;
    global $sellApiPriceKey;

    $json = file_get_contents($zebPayApiUrl);
    $result = json_decode($json, true);
    return [date(DATE_ISO8601), $result[$buyApiPriceKey], $result[$sellApiPriceKey]];
}



function getLastLineFromCsvAsArray($fileName) {
	$line = '';
    $f = fopen($fileName, 'r');
    $cursor = -1;

    fseek($f, $cursor, SEEK_END);
    $char = fgetc($f);

    while ($char === "\n" || $char === "\r") {
        fseek($f, $cursor--, SEEK_END);
        $char = fgetc($f);
    }

    while ($char !== false && $char !== "\n" && $char !== "\r") {
        $line = $char . $line;
        fseek($f, $cursor--, SEEK_END);
        $char = fgetc($f);
    }
    fclose($f);
    return explode(',', $line);
}



function getLastPriceData() {
    global $priceListFileName;

    return getLastLineFromCsvAsArray($priceListFileName);
}



function getLastAccessTimestamp() {
    global $accessLogFileName;

    if (!file_exists($accessLogFileName)) {
        return 0;
    }

    $lastLineArray = getLastLineFromCsvAsArray($accessLogFileName);
    return strtotime($lastLineArray[0]);
}



function isNewEntryRequired($lastData, $apiData) {
    if (strcmp($lastData[1], $apiData[1]) !== 0 || strcmp($lastData[2], $apiData[2]) !== 0) {
        return true;
    }
    return false;
}



function writePriceEntry($apiData) {
    global $priceListFileName;

    writeCsvToFile($priceListFileName, $apiData);
}



function writeAccessLog($apiData, $timeTaken) {
    global $accessLogFileName;

    $from = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : 'local';
    writeCsvToFile($accessLogFileName, [date(DATE_ISO8601), $timeTaken, $from]);
}



$lastData = [];
$apiData = [];
$newEntryRequired = false;
$apiCalled = false;
$written = false;

initSettings();

if (time() - getLastAccessTimestamp() > $apiCallWait) {
    $lastData = getLastPriceData();
    $apiData = getZebPayPriceData();
    $newEntryRequired = isNewEntryRequired($lastData, $apiData);
    $apiCalled = true;
}

if ($newEntryRequired === true) {
    writePriceEntry($apiData);
    $written = true;
}

echo json_encode(array_merge($apiData, ['apiCalled' => $apiCalled, 'written' => $written]));

$timeScriptEnds = microtime(true);
$timeTaken = intval(($timeScriptEnds - $timeScriptStarts) * 1000);
writeAccessLog($apiData, $timeTaken);
