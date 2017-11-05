<?php

$fileName = 'zebPayPrices.csv';
$apiUrl = 'https://www.zebapi.com/api/v1/market/ticker/btc/inr';
$timeZone = 'Asia/Kolkata';
$buyApiPriceKey = 'buy';
$sellApiPriceKey = 'sell';
$dateCsvPriceKey = 'Date';
$buyCsvPriceKey = 'Buy';
$sellCsvPriceKey = 'Sell';



function initSettings() {
    global $timeZone;
    date_default_timezone_set($timeZone);
}



function getZebPayPriceData() {
    global $apiUrl;
    global $buyApiPriceKey;
    global $sellApiPriceKey;
    $json = file_get_contents($apiUrl);
    $result = json_decode($json, true);
    return [date(DATE_ISO8601), $result[$buyApiPriceKey], $result[$sellApiPriceKey]];
}



function getLastPriceData() {
    global $fileName;

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



function isNewEntryRequired($lastData, $apiData) {
    if ($lastData[1] !== $apiData[1] || $lastData[2] !== $apiData[2]) {
        return true;
    }
    return false;
}



function writeEntryToFile($apiData) {
    global $fileName;
    global $dateCsvPriceKey;
    global $buyCsvPriceKey;
    global $sellCsvPriceKey;

    if (file_exists($fileName)) {
        $f = fopen($fileName, 'a');
        fputcsv($f, $apiData);
    } else {
        $f = fopen($fileName, 'w');
        fputcsv($f, [$dateCsvPriceKey, $buyCsvPriceKey, $sellCsvPriceKey]);
        fputcsv($f, $apiData);
    }
    fclose($f);
}





initSettings();
$apiData = getZebPayPriceData();
$lastData = getLastPriceData();
$newEntryRequired = isNewEntryRequired($lastData, $apiData);

$written = false;
if ($newEntryRequired === true) {
    writeEntryToFile($apiData);
    $written = true;
}

echo json_encode(array_merge($apiData, ['written' => $written]));