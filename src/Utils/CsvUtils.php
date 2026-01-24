<?php

namespace PriceTracker\Utils;

use Exception;
use League\Csv\Writer;

class CsvUtils {
    private $dataPath;

    public function __construct($dataPath) {
        $this->dataPath = $dataPath;
    }

    public function writeCsvToFile($fileName, $arrayToWrite) {
        $filePath = $this->dataPath . $fileName;

        // ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = Writer::createFromPath($filePath, 'a+');
        $writer->insertOne($arrayToWrite);
    }

    public function getLastLineFromCsvAsArray($fileName) {
        $filePath = $this->dataPath . $fileName;

        if (!file_exists($filePath)) {
             throw new Exception("File not found: " . $filePath);
        }

        $line = '';
        $f = fopen($filePath, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        while ($char === "\n" || $char === "\r") {
            fseek($f, --$cursor, SEEK_END);
            $char = fgetc($f);
        }

        while ($char !== false && $char !== "\n" && $char !== "\r") {
            $line = $char . $line;
            fseek($f, --$cursor, SEEK_END);
            $char = fgetc($f);
        }
        fclose($f);

        if (empty(trim($line))) {
             throw new Exception("Empty Last line read from file: " . $filePath);
        }

        return str_getcsv($line);
    }

    public function checkIfFileExists($fileName) {
        return file_exists($this->dataPath . $fileName);
    }
}
