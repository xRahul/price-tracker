<?php

require __DIR__ . '/vendor/autoload.php';

use PriceTracker\PriceTrackerScript;

date_default_timezone_set('Asia/Kolkata');

$exchangeCoinName = explode("-", $_GET['exchangeCoin']);
$exchangeName = $exchangeCoinName[0];
$coinName = $exchangeCoinName[1];

if (empty($exchangeName) || empty($coinName)) {
	echo 'Exchange or Coin not found in parameters';
} else {
	try {
		$script = new PriceTrackerScript($exchangeName, $coinName);
		$script->run();
	} catch (Exception $e) {
		echo "Exception: " . $e->getMessage();
	}
}