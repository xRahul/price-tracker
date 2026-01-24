<?php

namespace PriceTracker\Config;

use Exception;

class Exchange {
    private $exchangeList;

    public function __construct() {
        $this->exchangeList = require __DIR__ . '/exchanges.php';
    }

    public function getExchangeConfig($exchange, $coin) {
        if (array_key_exists($exchange, $this->exchangeList)) {
            if (array_key_exists($coin, $this->exchangeList[$exchange])) {
                return $this->exchangeList[$exchange][$coin];
            }
        }
        throw new Exception("Exchange and coin not found in the list: " . $exchange . ", " . $coin);
    }
}
