<?php

namespace PriceTracker\Utils;

use Exception;

class CsvUtils {
    static public function writeCsvToFile($fileName, $arrayToWrite) {
        self::checkIfFileExists($fileName);

        $f = fopen(__DIR__ . '/../data/' . $fileName, 'a');
        fputcsv($f, $arrayToWrite);
        fclose($f);
    }

    static public function getLastLineFromCsvAsArray($fileName) {
        self::checkIfFileExists($fileName);

        $line = '';
        $f = fopen(__DIR__ . '/../data/' . $fileName, 'r');
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

        if (empty(trim($line))) {
            throw new Exception("Empty Last line read from file: " . __DIR__ . '/../data/' . $fileName);
        }

        return explode(',', $line);
    }

    static public function checkIfFileExists($fileName) {
        if (!file_exists(__DIR__ . '/../data/' . $fileName)) {
            return false;
        }
        return true;
    }
}
