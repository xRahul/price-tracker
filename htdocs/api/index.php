<?php

require __DIR__ . '/vendor/autoload.php';

use PriceTracker\PriceTrackerScript;

date_default_timezone_set('Asia/Kolkata');

$exchangeName = $_GET['exchange'];

if (empty($exchangeName)) {
	throw new Exception('Exchange not mentioned');
}

$script = new PriceTrackerScript($exchangeName);
$script->run();