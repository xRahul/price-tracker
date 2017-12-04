<?php

require __DIR__ . '/vendor/autoload.php';

use PriceTracker\PriceTrackerScript;

date_default_timezone_set('Asia/Kolkata');

$exchangeName = $_GET['exchange'];
$coinName = $_GET['coin'];

if (empty($exchangeName) || empty($coinName)) {
	throw new Exception('Exchange or Coin not found in parameters');
}

$script = new PriceTrackerScript($exchangeName, $coinName);
$script->run();