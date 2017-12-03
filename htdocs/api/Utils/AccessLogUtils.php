<?php

namespace PriceTracker\Utils;

use PriceTracker\Utils\CsvUtils;
use PriceTracker\bo\AccessLogData;
use DateTime;

class AccessLogUtils {

    static private function getFileName($contextName) {
        $nowDateTime = new DateTime();
        $nowWeek = $nowDateTime->format("W");
        $nowYear = $nowDateTime->format("Y");
        return 'access-log-' . $contextName . '-' . $nowWeek . '-' . $nowYear . '.csv';
    }

    static private function createLog($timeTaken) {
        $fromIPAddress = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : 'local';
        $accessLog = new AccessLogData();
        $accessLog->setDateTime(date(DATE_ISO8601));
        $accessLog->setFromIPAddress($fromIPAddress);
        $accessLog->setTimeTaken($timeTaken);
        return $accessLog;
    }

    static private function writeLog($fileName, $accessLog) {
        CsvUtils::writeCsvToFile($fileName, $accessLog->toArray());
    }

    static public function log($contextName, $timeTaken) {
        $accessLogFileName = self::getFileName($contextName);
        $accessLog = self::createLog($timeTaken);
        self::writeLog($accessLogFileName, $accessLog);
    }
}
